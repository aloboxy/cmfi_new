<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class PayrollService
{
    /**
     * Generate payroll/payslip for a staff member.
     */
    public function generatePayslip(int $staffId, string $month, string $year, float $netSalary, int $branchId)
    {
        return DB::transaction(function () use ($staffId, $month, $year, $netSalary, $branchId) {
            // Check if already generated
            $existing = DB::table('payslip')
                ->where('staff_id', $staffId)
                ->where('month', $month)
                ->where('year', $year)
                ->first();

            if ($existing) {
                return $existing->id;
            }

            // Insert into payslip
            $payslipId = DB::table('payslip')->insertGetId([
                'staff_id' => $staffId,
                'month' => $month,
                'year' => $year,
                'net_salary' => $netSalary,
                'status' => 'unpaid',
                'branch_id' => $branchId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return $payslipId;
        });
    }

    /**
     * Record payslip payment.
     */
    public function paySalary(int $payslipId, int $accountId, string $paymentMethod, string $remarks)
    {
        return DB::transaction(function () use ($payslipId, $accountId, $paymentMethod, $remarks) {
            $payslip = DB::table('payslip')->where('id', $payslipId)->first();
            if (!$payslip || $payslip->status === 'paid') {
                return false;
            }

            // Mark as paid
            DB::table('payslip')->where('id', $payslipId)->update([
                'status' => 'paid',
                'payment_method' => $paymentMethod,
                'updated_at' => now(),
            ]);

            // Add transaction for accounting outflow
            DB::table('transactions')->insert([
                'account_id' => $accountId,
                'amount' => $payslip->net_salary,
                'type' => 'expense',
                'branch_id' => $payslip->branch_id,
                'date' => now()->toDateString(),
                'description' => "Salary payment for payslip ID: {$payslipId}",
                'created_at' => now(),
            ]);

            return true;
        });
    }
}
