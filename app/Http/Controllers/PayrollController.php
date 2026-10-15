<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Payslip;
use App\Models\PayslipDetail;
use App\Models\SalaryTemplate;
use App\Models\SalaryTemplateDetail;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PayrollController extends Controller
{
    // Salary payment list (legacy index)
    public function index(Request $request)
    {
        abort_unless(get_permission('salary_payment', 'is_view'), 403, translate('access_denied'));
        $branchId = $this->resolveBranchId($request);
        $stafflist = null;
        $month = null;
        $year = null;
        if ($request->has('search') || $request->input('month_year')) {
            $month_year = $request->input('month_year');
            $staff_role = $request->input('staff_role');
            if ($month_year) {
                $month = date('m', strtotime($month_year));
                $year = date('Y', strtotime($month_year));
            }
            $stafflist = $this->getEmployeePaymentList($branchId, $staff_role, $month, $year);
        }
        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();
        $roles = $this->getRoles();
        return view('payroll.payment', compact('stafflist','month','year','branches','roles'));
    }

    public function create($id, $month, $year, Request $request)
    {
        abort_unless(get_permission('salary_payment', 'is_add'), 403, translate('access_denied'));
        // branch restriction
        if (!is_superadmin_loggedin()) {
            $staff = Staff::where('id',$id)->where('branch_id', get_loggedin_branch_id())->firstOrFail();
        }
        if ($request->isMethod('post') && $request->has('paid')) {
            $data = $request->all();
            $data['staff_id'] = $id;
            $data['month'] = $month;
            $data['year'] = $year;
            $result = $this->savePayslip($data);
            if ($result['status']=='success') {
                set_alert('success', translate('information_has_been_saved_successfully'));
                return redirect($result['uri']);
            } else {
                set_alert('error', 'This Month Salary Already Paid !');
                return redirect()->route('payroll.index');
            }
        }
        $staff = $this->getEmployeePayment($id, $month, $year);
        abort_if(!$staff,404);
        // branch check
        if (!is_superadmin_loggedin() && $staff['branch_id'] != get_loggedin_branch_id()) abort(403);
        $payvia_list = DB::table('payment_types')->pluck('name','id');
        // fallback if payment_types empty
        if ($payvia_list->isEmpty()) $payvia_list = collect([1=>'Cash',2=>'Cheque',5=>'Bank']);
        $allowances = SalaryTemplateDetail::where('salary_template_id',$staff['salary_template_id'])->where('type',1)->get();
        $deductions = SalaryTemplateDetail::where('salary_template_id',$staff['salary_template_id'])->where('type',2)->get();
        $advance = DB::table('advance_salary')->where('staff_id',$id)->where('deduct_month',$month)->where('year',$year)->value('amount');
        return view('payroll.create', compact('staff','month','year','payvia_list','allowances','deductions','advance'));
    }

    public function invoice($id, $hash)
    {
        abort_unless(get_permission('salary_payment', 'is_view'), 403, translate('access_denied'));
        $salary = $this->getInvoice($id);
        abort_if(!$salary,404);
        if ($salary['hash'] !== $hash) abort(403, translate('access_denied'));
        if (!is_superadmin_loggedin() && $salary['branch_id'] != get_loggedin_branch_id()) abort(403);
        $detailsAllowance = PayslipDetail::where('payslip_id',$id)->where('type',1)->get();
        $detailsDeduction = PayslipDetail::where('payslip_id',$id)->where('type',2)->get();
        return view('payroll.invoice', compact('salary','detailsAllowance','detailsDeduction'));
    }

    public function salaryTemplate(Request $request)
    {
        abort_unless(get_permission('salary_template', 'is_view'), 403, translate('access_denied'));
        if ($request->isMethod('post')) {
            abort_unless(get_permission('salary_template', 'is_add'), 403, translate('access_denied'));
            $validated = $request->validate([
                'branch_id' => is_superadmin_loggedin() ? ['required','integer','exists:branch,id'] : ['nullable'],
                'template_name' => ['required','string','max:255'],
                'basic_salary' => ['required','numeric'],
                'overtime_rate' => ['nullable','numeric'],
                'allowance' => ['nullable','array'],
                'deduction' => ['nullable','array'],
            ]);
            $branchId = $this->resolveBranchId($request);
            $template = SalaryTemplate::create([
                'branch_id' => $branchId,
                'name' => $validated['template_name'],
                'basic_salary' => $validated['basic_salary'],
                'overtime_salary' => $validated['overtime_rate'] ?? 0,
            ]);
            $tid = $template->id;
            foreach ($request->input('allowance',[]) as $val) {
                if (!empty($val['name']) && $val['amount']!=='') {
                    SalaryTemplateDetail::create(['salary_template_id'=>$tid,'name'=>$val['name'],'amount'=>$val['amount'],'type'=>1]);
                }
            }
            foreach ($request->input('deduction',[]) as $val) {
                if (!empty($val['name']) && $val['amount']!=='') {
                    SalaryTemplateDetail::create(['salary_template_id'=>$tid,'name'=>$val['name'],'amount'=>$val['amount'],'type'=>2]);
                }
            }
            set_alert('success', translate('information_has_been_saved_successfully'));
            if ($request->expectsJson()) return response()->json(['status'=>'success','url'=>route('payroll.salary_template')]);
            return redirect()->route('payroll.salary_template');
        }
        $templates = SalaryTemplate::when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id', get_loggedin_branch_id()))->orderBy('id','asc')->get();
        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();
        return view('payroll.salary_template', compact('templates','branches'));
    }

    public function salaryTemplateEdit($id, Request $request)
    {
        abort_unless(get_permission('salary_template', 'is_edit'), 403, translate('access_denied'));
        $template = SalaryTemplate::findOrFail($id);
        if (!is_superadmin_loggedin() && $template->branch_id != get_loggedin_branch_id()) abort(403);
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'branch_id' => is_superadmin_loggedin() ? ['required','integer','exists:branch,id'] : ['nullable'],
                'template_name' => ['required','string','max:255'],
                'basic_salary' => ['required','numeric'],
                'overtime_rate' => ['nullable','numeric'],
                'allowance' => ['nullable','array'],
                'deduction' => ['nullable','array'],
            ]);
            $branchId = $this->resolveBranchId($request);
            $template->update([
                'name' => $validated['template_name'],
                'basic_salary' => $validated['basic_salary'],
                'overtime_salary' => $validated['overtime_rate'] ?? 0,
                'branch_id' => $branchId,
            ]);
            // handle allowances/deductions: delete missing? legacy updates existing and inserts new, but doesn't delete removed. We'll sync: remove all and reinsert to simplify, but keep old ids logic?
            // For simplicity, delete existing and re-create if no old ids provided logic differs. We'll support both.
            // If request has allowance with old ids, we update; otherwise we replace.
            // Simpler: delete all and reinsert from input
            SalaryTemplateDetail::where('salary_template_id',$id)->delete();
            foreach ($request->input('allowance',[]) as $val) {
                if (!empty($val['name']) && $val['amount']!=='') {
                    SalaryTemplateDetail::create(['salary_template_id'=>$id,'name'=>$val['name'],'amount'=>$val['amount'],'type'=>1]);
                }
            }
            foreach ($request->input('deduction',[]) as $val) {
                if (!empty($val['name']) && $val['amount']!=='') {
                    SalaryTemplateDetail::create(['salary_template_id'=>$id,'name'=>$val['name'],'amount'=>$val['amount'],'type'=>2]);
                }
            }
            set_alert('success', translate('information_has_been_updated_successfully'));
            if ($request->expectsJson()) return response()->json(['status'=>'success','url'=>route('payroll.salary_template')]);
            return redirect()->route('payroll.salary_template');
        }
        $allowances = SalaryTemplateDetail::where('salary_template_id',$id)->where('type',1)->get();
        $deductions = SalaryTemplateDetail::where('salary_template_id',$id)->where('type',2)->get();
        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();
        return view('payroll.salary_template_edit', compact('template','allowances','deductions','branches'));
    }

    public function salaryTemplateDelete(Request $request, $id)
    {
        abort_unless(get_permission('salary_template', 'is_delete'), 403, translate('access_denied'));
        $template = SalaryTemplate::findOrFail($id);
        if (!is_superadmin_loggedin() && $template->branch_id != get_loggedin_branch_id()) abort(403);
        SalaryTemplateDetail::where('salary_template_id',$id)->delete();
        $template->delete();
        if ($request->expectsJson()) return response()->json(['status'=>'success']);
        set_alert('success', translate('information_has_been_deleted_successfully'));
        return redirect()->route('payroll.salary_template');
    }

    public function salaryTemplateView($id)
    {
        $template = SalaryTemplate::findOrFail($id);
        $allowances = SalaryTemplateDetail::where('salary_template_id',$id)->where('type',1)->get();
        $deductions = SalaryTemplateDetail::where('salary_template_id',$id)->where('type',2)->get();
        return view('payroll.salary_template_view', compact('template','allowances','deductions'));
    }

    public function salaryAssign(Request $request)
    {
        abort_unless(get_permission('salary_assign', 'is_view'), 403, translate('access_denied'));
        $branchId = $this->resolveBranchId($request);
        // branch for assign filtering: if superadmin uses selected branch else own
        $filterBranch = is_superadmin_loggedin() ? ($request->input('branch_id') ? (int)$request->input('branch_id') : null) : get_loggedin_branch_id();
        if ($request->has('search')) {
            $staff_role = $request->input('staff_role');
            $designation_id = $request->input('designation_id');
            $branchForSearch = $filterBranch ?? $branchId;
            $stafflist = $this->getEmployeeList($branchForSearch, $staff_role, $designation_id);
        } else {
            $stafflist = null;
        }
        if ($request->has('assign')) {
            abort_unless(get_permission('salary_assign', 'is_add'), 403, translate('access_denied'));
            $stafflistInput = $request->input('stafflist',[]);
            foreach ($stafflistInput as $val){
                $template_id = $val['template_id'] ?? 0;
                if (empty($template_id)) $template_id=0;
                DB::table('staff')->where('id',$val['id'])->update(['salary_template_id'=>$template_id]);
            }
            set_alert('success', translate('information_has_been_saved_successfully'));
            return redirect()->route('payroll.salary_assign');
        }
        $effectiveBranch = $filterBranch ?? $branchId ?? get_loggedin_branch_id();
        $designationlist = DB::table('staff_designation')->when($effectiveBranch, fn($q)=>$q->where('branch_id',$effectiveBranch))->pluck('name','id');
        $templatelist = SalaryTemplate::when($effectiveBranch, fn($q)=>$q->where('branch_id',$effectiveBranch))->pluck('name','id');
        $roles = $this->getRoles();
        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();
        return view('payroll.salary_assign', compact('stafflist','designationlist','templatelist','roles','branches','effectiveBranch'));
    }

    // helpers
    private function resolveBranchId(Request $request): int
    {
        if (is_superadmin_loggedin()) {
            $bid = $request->input('branch_id');
            if (!empty($bid)) return (int)$bid;
            // legacy: get_branch_id returns post branch_id, else maybe loggedin branch (null for superadmin)
            return (int)($bid ?? get_loggedin_branch_id() ?? 0);
        }
        return (int) get_loggedin_branch_id();
    }

    private function getRoles()
    {
        return DB::table('roles')->whereNotIn('id',[1,6,7])->pluck('name','id');
    }

    private function getEmployeeList($branch_id, $role_id, $designation)
    {
        $q = DB::table('staff')
            ->select('staff.*','staff_designation.name as designation_name','staff_department.name as department_name','login_credential.role as role_id','roles.name as role')
            ->join('login_credential', function($j){ $j->on('login_credential.user_id','=','staff.id')->whereNotIn('login_credential.role',[6,7]); })
            ->leftJoin('roles','roles.id','=','login_credential.role')
            ->leftJoin('staff_designation','staff_designation.id','=','staff.designation')
            ->leftJoin('staff_department','staff_department.id','=','staff.department')
            ->where('staff.branch_id',$branch_id)
            ->where('login_credential.active',1);
        if (!empty($role_id)) $q->where('login_credential.role',$role_id);
        if (!empty($designation)) $q->where('staff.designation',$designation);
        return $q->get();
    }

    private function getEmployeePaymentList($branch_id='', $role_id='', $month='', $year='')
    {
        $q = DB::table('staff')
            ->select('staff.*','staff_designation.name as designation_name','staff_department.name as department_name','login_credential.role as role_id','roles.name as role', DB::raw('IFNULL(payslip.id,0) as salary_id'), 'payslip.hash as salary_hash','salary_template.name as template_name','salary_template.basic_salary')
            ->join('login_credential', 'login_credential.user_id','=','staff.id')
            ->leftJoin('roles','roles.id','=','login_credential.role')
            ->leftJoin('staff_designation','staff_designation.id','=','staff.designation')
            ->leftJoin('staff_department','staff_department.id','=','staff.department')
            ->leftJoin('payslip', function($j) use($month,$year){
                $j->on('payslip.staff_id','=','staff.id');
                if($month) $j->where('payslip.month','=',$month);
                if($year) $j->where('payslip.year','=',$year);
            })
            ->leftJoin('salary_template','salary_template.id','=','staff.salary_template_id')
            ->where('staff.branch_id',$branch_id)
            ->where('login_credential.active',1)
            ->where('staff.salary_template_id','!=',0);
        if (!empty($role_id)) $q->where('login_credential.role',$role_id);
        return $q->get();
    }

    private function getEmployeePayment($staff_id, $month, $year)
    {
        $sql = "SELECT staff.*, staff_designation.name as designation_name, staff_department.name as department_name, login_credential.role as role_id, roles.name as role,
        salary_template.name as template_name, salary_template.basic_salary, salary_template.overtime_salary, advance_salary.amount as advance_amount FROM staff INNER JOIN
        login_credential ON login_credential.user_id = staff.id LEFT JOIN roles ON roles.id = login_credential.role LEFT JOIN staff_designation ON
        staff_designation.id = staff.designation LEFT JOIN staff_department ON
        staff_department.id = staff.department LEFT JOIN salary_template ON
        salary_template.id = staff.salary_template_id LEFT JOIN advance_salary ON advance_salary.staff_id = staff.id AND 
        advance_salary.deduct_month = ? AND advance_salary.year = ? WHERE
        staff.id = ?";
        return DB::selectOne($sql, [$month, $year, $staff_id]) ? (array)DB::selectOne($sql, [$month, $year, $staff_id]) : null;
    }

    private function getInvoice($id)
    {
        $row = DB::table('payslip')
            ->select('payslip.*','staff.name as staff_name','staff.mobileno', DB::raw('IFNULL(staff_designation.name,"N/A") as designation_name'), DB::raw('IFNULL(staff_department.name,"N/A") as department_name'),'branch.school_name','branch.email as school_email','branch.mobileno as school_mobileno','branch.address as school_address')
            ->leftJoin('staff','staff.id','=','payslip.staff_id')
            ->leftJoin('staff_designation','staff_designation.id','=','staff.designation')
            ->leftJoin('staff_department','staff_department.id','=','staff.department')
            ->leftJoin('branch','branch.id','=','staff.branch_id')
            ->where('payslip.id',$id)
            ->first();
        return $row ? (array)$row : null;
    }

    private function savePayslip($data)
    {
        $staff_id = $data['staff_id'];
        $month = $data['month'];
        $year = $data['year'];
        $total_allowance = $data['total_allowance'] ?? 0;
        $total_deduction = $data['total_deduction'] ?? 0;
        $net_salary = $data['net_salary'] ?? 0;
        $overtime_hour = $data['overtime_total_hour'] ?? 0;
        $overtime_amount = $data['overtime_amount'] ?? 0;
        $salary_template_id = $data['salary_template_id'] ?? 0;
        $branchID = $data['branch_id'] ?? $this->resolveBranchId(request());

        $ad_salary = DB::table('advance_salary')->where('staff_id',$staff_id)->where('deduct_month',$month)->where('year',$year)->first();
        $exist = DB::table('payslip')->where('staff_id',$staff_id)->where('month',$month)->where('year',$year)->count();
        if ($exist==0) {
            $bill_no = $this->getBillNo();
            $arrayPayslip = [
                'staff_id'=>$staff_id,'month'=>$month,'year'=>$year,'basic_salary'=>$data['basic_salary'],'total_allowance'=>$total_allowance,'total_deduction'=>$total_deduction,'net_salary'=>$net_salary,'bill_no'=>$bill_no,'remarks'=>$data['remarks'] ?? '','hash'=>Str::random(32),'pay_via'=>$data['pay_via'],'branch_id'=>$branchID,'paid_by'=>get_loggedin_user_id(),'created_at'=>now(),
            ];
            $payslip_id = DB::table('payslip')->insertGetId($arrayPayslip);
            $payslipData=[];
            $getTemplate = SalaryTemplateDetail::where('salary_template_id',$salary_template_id)->get();
            foreach($getTemplate as $row){
                $payslipData[]=['payslip_id'=>$payslip_id,'name'=>$row->name,'amount'=>$row->amount,'type'=>$row->type];
            }
            if (!empty($overtime_hour) && $overtime_hour!=0){
                $payslipData[]=['payslip_id'=>$payslip_id,'name'=>"Overtime Salary (".$overtime_hour." Hour)",'amount'=>$overtime_amount,'type'=>1];
            }
            if ($ad_salary){
                $payslipData[]=['payslip_id'=>$payslip_id,'name'=>"Advance Salary",'amount'=>$ad_salary->amount,'type'=>2];
            }
            if (!empty($payslipData)) DB::table('payslip_details')->insert($payslipData);
            // voucher transaction if account_id provided
            if (isset($data['account_id']) && !empty($data['account_id'])) {
                $this->saveTransaction(['account_id'=>$data['account_id'],'amount'=>$net_salary,'month'=>$month,'year'=>$year,'branch_id'=>$branchID]);
            }
            $payslip_url = url('payroll/invoice/'.$payslip_id.'/'.$arrayPayslip['hash']);
            return ['status'=>'success','uri'=>$payslip_url];
        } else {
            return ['status'=>'failed'];
        }
    }

    private function getBillNo()
    {
        $max = DB::table('payslip')->max(DB::raw('CAST(bill_no AS UNSIGNED)'));
        return $max ? str_pad($max+1,6,'0',STR_PAD_LEFT) : '000001';
    }

    private function saveTransaction($data)
    {
        $branchID = $data['branch_id'];
        $accountID = $data['account_id'];
        $amount = $data['amount'];
        $month = $data['month'];
        $year = $data['year'];
        $description = date('M-Y', strtotime($year.'-'.$month))." Paying Employees Salaries";
        $qbal = DB::table('accounts')->where('id',$accountID)->first();
        if (!$qbal) return;
        $cbal = $qbal->balance;
        $bal = ($cbal - $amount);
        $arrayHead = ['name'=>'Employees Salary Payment','type'=>'expense','system'=>1,'branch_id'=>$branchID];
        $head = DB::table('voucher_head')->where($arrayHead)->first();
        if ($head) $voucher_headID=$head->id; else { $voucher_headID=DB::table('voucher_head')->insertGetId($arrayHead); }
        $arrayTransactions = ['account_id'=>$accountID,'voucher_head_id'=>$voucher_headID,'type'=>'expense','system'=>1,'branch_id'=>$branchID];
        $exists = DB::table('transactions')->where($arrayTransactions)->where('description',$description)->first();
        if ($exists){
            DB::table('transactions')->where('id',$exists->id)->update(['amount'=>DB::raw('amount+'.$amount),'dr'=>DB::raw('dr+'.$amount),'bal'=>$bal]);
        } else {
            DB::table('transactions')->insert(array_merge($arrayTransactions,['date'=>date('Y-m-d'),'ref'=>'','amount'=>$amount,'dr'=>$amount,'cr'=>0,'bal'=>$bal,'pay_via'=>5,'description'=>$description]));
        }
        DB::table('accounts')->where('id',$accountID)->update(['balance'=>$bal]);
    }
}
