<?php

namespace App\Services;

use App\Models\Student;
use Illuminate\Support\Facades\DB;

class FeeService
{
    /**
     * Allocate fees to a student.
     */
    public function allocateFeeToStudent(int $studentId, int $feeGroupId, int $branchId)
    {
        return DB::transaction(function () use ($studentId, $feeGroupId, $branchId) {
            // Check if already allocated
            $existing = DB::table('fee_allocation')
                ->where('student_id', $studentId)
                ->where('group_id', $feeGroupId)
                ->first();

            if ($existing) {
                return $existing->id;
            }

            return DB::table('fee_allocation')->insertGetId([
                'student_id' => $studentId,
                'group_id' => $feeGroupId,
                'branch_id' => $branchId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }

    /**
     * Record a fee payment.
     */
    public function recordPayment(array $details)
    {
        return DB::transaction(function () use ($details) {
            // Insert fee payment history
            $paymentId = DB::table('fee_payment_history')->insertGetId([
                'allocation_id' => $details['allocation_id'],
                'type_id' => $details['type_id'],
                'collect_by' => $details['collect_by'] ?? 'System',
                'amount' => $details['amount'],
                'discount' => $details['discount'] ?? 0.00,
                'fine' => $details['fine'] ?? 0.00,
                'pay_via' => $details['pay_via'],
                'remarks' => $details['remarks'] ?? '',
                'date' => $details['date'] ?? now()->toDateString(),
                'created_at' => now(),
            ]);

            // Add transaction link/record for general accounting
            DB::table('transactions')->insert([
                'account_id' => $details['account_id'],
                'amount' => $details['amount'],
                'type' => 'income',
                'branch_id' => $details['branch_id'],
                'date' => $details['date'] ?? now()->toDateString(),
                'description' => 'Fee collection payment ID: ' . $paymentId,
                'created_at' => now(),
            ]);

            return $paymentId;
        });
    }

    /**
     * Get remaining balance for student's allocated fees.
     */
    public function getStudentBalance(int $studentId)
    {
        $allocated = DB::table('fee_allocation')
            ->join('fee_groups_details', 'fee_allocation.group_id', '=', 'fee_groups_details.fee_groups_id')
            ->where('fee_allocation.student_id', $studentId)
            ->sum('fee_groups_details.amount');

        $paid = DB::table('fee_payment_history')
            ->join('fee_allocation', 'fee_payment_history.allocation_id', '=', 'fee_allocation.id')
            ->where('fee_allocation.student_id', $studentId)
            ->sum('fee_payment_history.amount');

        return max(0, $allocated - $paid);
    }
}
