<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdvanceSalaryController extends Controller
{
    // manage application (admin)
    public function index(Request $request)
    {
        abort_unless(get_permission('advance_salary_manage', 'is_view'), 403, translate('access_denied'));
        if ($request->has('update')) {
            abort_unless(get_permission('advance_salary_manage', 'is_add'), 403, translate('access_denied'));
            $request->validate(['status'=>'required','comments'=>'nullable','id'=>'required|integer']);
            DB::table('advance_salary')->where('id',$request->input('id'))->update([
                'issued_by'=>get_loggedin_user_id(),'paid_date'=>now(),'status'=>$request->input('status'),'comments'=>$request->input('comments')
            ]);
            set_alert('success', translate('information_has_been_updated_successfully'));
            return redirect()->route('advancesalary.index');
        }
        $month=''; $year='';
        if ($request->has('search')) {
            $month_year = $request->input('month_year');
            if ($month_year){ $month=date('m',strtotime($month_year)); $year=date('Y',strtotime($month_year)); }
        }
        $branch_id = $this->resolveBranchId($request);
        $advanceslist = $this->getAdvanceSalaryList($month,$year,$branch_id);
        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();
        return view('advance_salary.index', compact('advanceslist','month','year','branches'));
    }

    public function save(Request $request)
    {
        abort_unless(get_permission('advance_salary_manage', 'is_add'), 403, translate('access_denied'));
        $request->validate([
            'branch_id'=> is_superadmin_loggedin() ? ['required','integer'] : ['nullable'],
            'staff_role'=> ['required'],
            'staff_id'=> ['required','integer'],
            'amount'=> ['required','numeric'],
            'month_year'=> ['required'],
            'reason'=> ['nullable','string'],
        ]);
        $staff_id = $request->input('staff_id');
        $amount = $request->input('amount');
        $month_year = $request->input('month_year');
        // checks
        $chk = $this->checkSalary($staff_id,$amount);
        if ($chk!==true) return response()->json(['status'=>'fail','error'=>['amount'=>$chk]]);
        $chk2 = $this->checkAdvanceMonth($staff_id,$month_year);
        if ($chk2!==true) return response()->json(['status'=>'fail','error'=>['month_year'=>$chk2]]);
        $branch_id = $this->resolveBranchId($request);
        DB::table('advance_salary')->insert([
            'staff_id'=>$staff_id,
            'deduct_month'=>date('m',strtotime($month_year)),
            'year'=>date('Y',strtotime($month_year)),
            'amount'=>$amount,
            'reason'=>$request->input('reason'),
            'issued_by'=>get_loggedin_user_id(),
            'paid_date'=>now(),
            'status'=>2,
            'branch_id'=>$branch_id,
            'request_date'=>now(),
            'create_at'=>now(),
        ]);
        set_alert('success', translate('information_has_been_saved_successfully'));
        return response()->json(['status'=>'success','url'=>route('advancesalary.index')]);
    }

    public function delete(Request $request, $id)
    {
        abort_unless(get_permission('advance_salary_manage', 'is_delete'), 403, translate('access_denied'));
        $q = DB::table('advance_salary')->where('id',$id);
        if (!is_superadmin_loggedin()) $q->where('branch_id', get_loggedin_branch_id());
        $q->delete();
        if ($request->expectsJson()) return response()->json(['status'=>'success']);
        return redirect()->route('advancesalary.index');
    }

    // my application
    public function request(Request $request)
    {
        abort_unless(get_permission('advance_salary_request', 'is_view'), 403, translate('access_denied'));
        $month=''; $year='';
        if ($request->has('search')) {
            $month_year=$request->input('month_year');
            if($month_year){ $month=date('m',strtotime($month_year)); $year=date('Y',strtotime($month_year)); }
        }
        $staff_id = get_loggedin_user_id();
        $advanceslist = $this->getAdvanceSalaryList($month,$year,'', $staff_id);
        return view('advance_salary.request', compact('advanceslist'));
    }

    public function requestSave(Request $request)
    {
        abort_unless(get_permission('advance_salary_request', 'is_add'), 403, translate('access_denied'));
        $request->validate([
            'amount'=> ['required','numeric'],
            'month_year'=> ['required'],
            'reason'=> ['nullable','string'],
        ]);
        $staff_id = get_loggedin_user_id();
        $amount = $request->input('amount');
        $month_year = $request->input('month_year');
        $chk = $this->checkSalary($staff_id,$amount);
        if ($chk!==true) return response()->json(['status'=>'fail','error'=>['amount'=>$chk]]);
        $chk2 = $this->checkAdvanceMonth($staff_id,$month_year);
        if ($chk2!==true) return response()->json(['status'=>'fail','error'=>['month_year'=>$chk2]]);
        DB::table('advance_salary')->insert([
            'staff_id'=>$staff_id,
            'deduct_month'=>date('m',strtotime($month_year)),
            'year'=>date('Y',strtotime($month_year)),
            'amount'=>$amount,
            'reason'=>$request->input('reason'),
            'branch_id'=>get_loggedin_branch_id(),
            'status'=>1,
            'request_date'=>now(),
            'create_at'=>now(),
        ]);
        set_alert('success', translate('information_has_been_saved_successfully'));
        return response()->json(['status'=>'success','url'=>route('advancesalary.request')]);
    }

    public function requestDelete(Request $request, $id)
    {
        abort_unless(get_permission('advance_salary_request', 'is_delete'), 403, translate('access_denied'));
        DB::table('advance_salary')->where('staff_id', get_loggedin_user_id())->where('id',$id)->where('status',1)->delete();
        if ($request->expectsJson()) return response()->json(['status'=>'success']);
        return back();
    }

    public function getRequestDetails(Request $request)
    {
        $id = $request->input('id');
        $row = DB::table('advance_salary')->where('id',$id)->first();
        return view('advance_salary.modal_request_details', compact('row'));
    }

    private function getAdvanceSalaryList($month='',$year='',$branch_id='',$staff_id='')
    {
        $q = DB::table('advance_salary')
            ->select('advance_salary.*','staff.name','staff.staff_id as uniqid','staff.photo','lc.role as role_id','roles.name as role')
            ->join('staff','staff.id','=','advance_salary.staff_id')
            ->leftJoin('login_credential as lc', function($j){ $j->on('lc.user_id','=','staff.id')->whereNotIn('lc.role',[6,7]); })
            ->leftJoin('roles','roles.id','=','lc.role');
        if (!empty($month)){$q->where('advance_salary.deduct_month',$month)->where('advance_salary.year',$year);}
        if (!empty($branch_id)) $q->where('advance_salary.branch_id',$branch_id);
        if (!empty($staff_id)) $q->where('advance_salary.staff_id',$staff_id);
        if (!is_superadmin_loggedin() && empty($branch_id) && empty($staff_id)){
            // when manage list without branch param, filter by own branch for non-superadmin
            $q->where('advance_salary.branch_id', get_loggedin_branch_id());
        }
        return $q->orderBy('advance_salary.id','desc')->get();
    }

    private function checkSalary($staff_id,$amount)
    {
        $q = DB::table('staff')->where('id',$staff_id)->first();
        if (empty($q->salary_template_id) || $q->salary_template_id==0) return 'This Employee Is Not Allocated Salary !';
        $basic = DB::table('salary_template')->where('id',$q->salary_template_id)->value('basic_salary');
        if ($amount > $basic) return 'Your Advance Amount Exceeds Basic Salary !';
        return true;
    }

    private function checkAdvanceMonth($staff_id,$month)
    {
        $m = date('m',strtotime($month)); $y=date('Y',strtotime($month));
        $get_advance = DB::table('advance_salary')->where('staff_id',$staff_id)->where('deduct_month',$m)->where('year',$y)->where('status',2)->count();
        $get_salary = DB::table('payslip')->where('staff_id',$staff_id)->where('month',$m)->where('year',$y)->count();
        if ($get_advance==0 && $get_salary==0) return true;
        return 'This Month Salary Already Paid Or Requested !';
    }

    private function resolveBranchId(Request $request): int
    {
        if (is_superadmin_loggedin()){
            $bid=$request->input('branch_id');
            if(!empty($bid)) return (int)$bid;
            return (int)(get_loggedin_branch_id() ?? 0);
        }
        return (int)get_loggedin_branch_id();
    }
}
