<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\LeaveCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(get_permission('leave_manage', 'is_view'), 403, translate('access_denied'));
        if ($request->has('update')) {
            abort_unless(get_permission('leave_manage', 'is_add'), 403, translate('access_denied'));
            $request->validate(['status'=>'required','comments'=>'nullable','id'=>'required']);
            DB::table('leave_application')->where('id',$request->input('id'))->update(['approved_by'=>get_loggedin_user_id(),'status'=>$request->input('status'),'comments'=>$request->input('comments')]);
            set_alert('success', translate('information_has_been_updated_successfully'));
            return redirect()->route('leave.index');
        }
        $where=[];
        $branch_id = is_superadmin_loggedin() ? null : get_loggedin_branch_id();
        if (!empty($branch_id)) $where['la.branch_id']=$branch_id;
        if ($request->has('search')) {
            $user_role=$request->input('role_id');
            if($user_role) $where['la.role_id']=$user_role;
        }
        $leavelist = $this->getLeaveList($where);
        $roles = DB::table('roles')->pluck('name','id');
        return view('leave.index', compact('leavelist','roles'));
    }

    public function save(Request $request)
    {
        abort_unless(get_permission('leave_manage', 'is_add'), 403, translate('access_denied'));
        $request->validate([
            'branch_id'=> is_superadmin_loggedin() ? ['required'] : ['nullable'],
            'user_role'=> ['required'],
            'applicant_id'=> ['required','integer'],
            'leave_category'=> ['required','integer'],
            'daterange'=> ['required','string'],
            'attachment_file'=> ['nullable','file','max:2048','mimes:pdf,doc,xls,docx,xlsx,jpg,jpeg,png,gif,bmp'],
            'reason'=> ['nullable','string'],
            'comments'=> ['nullable','string'],
        ]);
        // custom checks
        $dateCheck = $this->dateCheck($request->input('daterange'), $request);
        if ($dateCheck!==true) return response()->json(['status'=>'fail','error'=>['daterange'=>$dateCheck]]);
        $leaveCheck = $this->leaveCheck($request->input('leave_category'), $request);
        if ($leaveCheck!==true) return response()->json(['status'=>'fail','error'=>['leave_category'=>$leaveCheck]]);

        $applicant_id = $request->input('applicant_id');
        $role_id = $request->input('user_role');
        $leave_type_id = $request->input('leave_category');
        $branch_id = $this->resolveBranchId($request);
        $daterange = explode(' - ', $request->input('daterange'));
        $start_date = date('Y-m-d', strtotime($daterange[0]));
        $end_date = date('Y-m-d', strtotime($daterange[1]));
        $leave_days = (new \DateTime($start_date))->diff(new \DateTime($end_date))->format('%a')+1;
        $orig=''; $enc='';
        if ($request->hasFile('attachment_file')){
            $file=$request->file('attachment_file');
            $orig=$file->getClientOriginalName();
            $enc=uniqid().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/attachments/leave'), $enc);
        }
        DB::table('leave_application')->insert([
            'user_id'=>$applicant_id,'role_id'=>$role_id,'branch_id'=>$branch_id,'session_id'=>get_session_id(),'category_id'=>$leave_type_id,'reason'=>$request->input('reason'),'start_date'=>$start_date,'end_date'=>$end_date,'leave_days'=>$leave_days,'status'=>2,'orig_file_name'=>$orig,'enc_file_name'=>$enc,'apply_date'=>now(),'approved_by'=>get_loggedin_user_id(),'comments'=>$request->input('comments',''),'created_at'=>now(),'updated_at'=>now(),
        ]);
        set_alert('success', translate('information_has_been_saved_successfully'));
        return response()->json(['status'=>'success','url'=>route('leave.index')]);
    }

    public function request(Request $request)
    {
        abort_unless(get_permission('leave_request', 'is_view'), 403, translate('access_denied'));
        if ($request->has('save')) {
            abort_unless(get_permission('leave_request', 'is_add'), 403, translate('access_denied'));
            $request->validate([
                'leave_category'=> ['required','integer'],
                'daterange'=> ['required','string'],
                'attachment_file'=> ['nullable','file','max:2048','mimes:pdf,doc,xls,docx,xlsx,jpg,jpeg,png,gif,bmp'],
                'reason'=> ['nullable','string'],
            ]);
            $dateCheck = $this->dateCheck($request->input('daterange'), $request);
            if ($dateCheck!==true) return back()->withErrors(['daterange'=>$dateCheck])->withInput();
            $leaveCheck = $this->leaveCheck($request->input('leave_category'), $request);
            if ($leaveCheck!==true) return back()->withErrors(['leave_category'=>$leaveCheck])->withInput();
            $leave_type_id = $request->input('leave_category');
            $branch_id = get_loggedin_branch_id();
            $daterange = explode(' - ', $request->input('daterange'));
            $start_date = date('Y-m-d', strtotime($daterange[0]));
            $end_date = date('Y-m-d', strtotime($daterange[1]));
            $leave_days = (new \DateTime($start_date))->diff(new \DateTime($end_date))->format('%a')+1;
            $orig=''; $enc='';
            if ($request->hasFile('attachment_file')){
                $file=$request->file('attachment_file');
                $orig=$file->getClientOriginalName();
                $enc=uniqid().'.'.$file->getClientOriginalExtension();
                $file->move(public_path('uploads/attachments/leave'), $enc);
            }
            DB::table('leave_application')->insert([
                'user_id'=>get_loggedin_user_id(),'role_id'=>loggedin_role_id(),'session_id'=>get_session_id(),'category_id'=>$leave_type_id,'reason'=>$request->input('reason'),'branch_id'=>$branch_id,'start_date'=>$start_date,'end_date'=>$end_date,'leave_days'=>$leave_days,'status'=>1,'orig_file_name'=>$orig,'enc_file_name'=>$enc,'apply_date'=>now(),'approved_by'=>0,'comments'=>'','created_at'=>now(),'updated_at'=>now(),
            ]);
            set_alert('success', translate('information_has_been_saved_successfully'));
            return redirect()->route('leave.request');
        }
        $where=['la.user_id'=>get_loggedin_user_id(),'la.role_id'=>loggedin_role_id()];
        $leavelist = $this->getLeaveList($where);
        $categories = LeaveCategory::when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id',get_loggedin_branch_id()))->where('role_id', loggedin_role_id())->get();
        return view('leave.request', compact('leavelist','categories'));
    }

    public function delete($id)
    {
        abort_unless(get_permission('leave_manage', 'is_delete'), 403, translate('access_denied'));
        $q=DB::table('leave_application')->where('id',$id);
        if (!is_superadmin_loggedin()) $q->where('branch_id',get_loggedin_branch_id());
        $row=$q->first();
        if($row && $row->enc_file_name){ $f=public_path('uploads/attachments/leave/'.$row->enc_file_name); if(file_exists($f)) unlink($f); }
        $q->delete();
        if(request()->expectsJson()) return response()->json(['status'=>'success']);
        return back();
    }

    public function requestDelete($id)
    {
        $where=['status'=>1,'user_id'=>get_loggedin_user_id(),'role_id'=>loggedin_role_id(),'id'=>$id];
        $app=DB::table('leave_application')->where($where)->first();
        if($app){
            if($app->enc_file_name){ $f=public_path('uploads/attachments/leave/'.$app->enc_file_name); if(file_exists($f)) unlink($f); }
            DB::table('leave_application')->where($where)->delete();
        }
        if(request()->expectsJson()) return response()->json(['status'=>'success']);
        return back();
    }

    public function category(Request $request)
    {
        abort_if(!get_permission('leave_category','is_view'),403);
        if ($request->has('save')) {
            abort_unless(get_permission('leave_category','is_add'),403);
            $request->validate([
                'branch_id'=> is_superadmin_loggedin()?['required']:['nullable'],
                'leave_category'=>['required','string'],
                'leave_days'=>['required','integer','min:1'],
                'role_id'=>['required','integer'],
            ]);
            $unique=$this->uniqueCategoryCheck($request->input('leave_category'), $request->input('role_id'), $this->resolveBranchId($request));
            if(!$unique) return back()->withErrors(['leave_category'=>translate('already_taken')])->withInput();
            DB::table('leave_category')->insert(['branch_id'=>$this->resolveBranchId($request),'name'=>$request->input('leave_category'),'role_id'=>$request->input('role_id'),'days'=>$request->input('leave_days')]);
            set_alert('success', translate('information_has_been_saved_successfully'));
            return redirect()->route('leave.category');
        }
        $categories = DB::table('leave_category')->when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id',get_loggedin_branch_id()))->get();
        $branches = is_superadmin_loggedin()?Branch::orderBy('name')->get():collect();
        $roles = DB::table('roles')->pluck('name','id');
        return view('leave.category', compact('categories','branches','roles'));
    }

    public function categoryEdit(Request $request)
    {
        abort_unless(get_permission('leave_category','is_edit'),403);
        $request->validate([
            'category_id'=>['required','integer'],
            'branch_id'=> is_superadmin_loggedin()?['required']:['nullable'],
            'leave_category'=>['required','string'],
            'leave_days'=>['required','integer','min:1'],
            'role_id'=>['required','integer'],
        ]);
        $bid=$this->resolveBranchId($request);
        $unique=$this->uniqueCategoryCheck($request->input('leave_category'), $request->input('role_id'), $bid, $request->input('category_id'));
        if(!$unique) return response()->json(['status'=>'fail','error'=>['leave_category'=>translate('already_taken')]]);
        DB::table('leave_category')->where('id',$request->input('category_id'))->update(['branch_id'=>$bid,'name'=>$request->input('leave_category'),'role_id'=>$request->input('role_id'),'days'=>$request->input('leave_days')]);
        set_alert('success', translate('information_has_been_updated_successfully'));
        return response()->json(['status'=>'success','url'=>route('leave.category')]);
    }

    public function categoryDelete($id)
    {
        abort_unless(get_permission('leave_category','is_delete'),403);
        $q=DB::table('leave_category')->where('id',$id);
        if(!is_superadmin_loggedin()) $q->where('branch_id',get_loggedin_branch_id());
        $q->delete();
        if(request()->expectsJson()) return response()->json(['status'=>'success']);
        set_alert('success', translate('information_has_been_deleted_successfully'));
        return redirect()->route('leave.category');
    }

    public function getCategory(Request $request)
    {
        $roleID=$request->input('role_id');
        $branchID=is_superadmin_loggedin()?$request->input('branch_id'):get_loggedin_branch_id();
        if (empty($roleID) || empty($branchID)) return response('<option value="">'.translate('select').'</option>');
        $rows=DB::table('leave_category')->where('branch_id',$branchID)->where('role_id',$roleID)->get();
        if($rows->isEmpty()) return response('<option value="">'.translate('no_information_available').'</option>');
        $html='<option value="">'.translate('select').'</option>';
        foreach($rows as $row) $html.='<option value="'.$row->id.'">'.e($row->name).' ('.$row->days.')</option>';
        return response($html);
    }

    public function download($id)
    {
        $leave=DB::table('leave_application')->where('id',$id)->first();
        abort_if(!$leave,404);
        // permission: owner or manage
        $file=public_path('uploads/attachments/leave/'.$leave->enc_file_name);
        if(!file_exists($file)) abort(404);
        return response()->download($file, $leave->orig_file_name);
    }

    public function getRequestDetails(Request $request)
    {
        $id=$request->input('id');
        $leave=$this->getLeaveList(['la.id'=>$id], true);
        return view('leave.modal_request_details', compact('leave'));
    }

    public function getApprovelLeaveDetails(Request $request)
    {
        $id=$request->input('id');
        $leave=$this->getLeaveList(['la.id'=>$id], true);
        return view('leave.approvel_modal', compact('leave'));
    }

    // helpers
    private function getLeaveList($where=[], $single=false)
    {
        $q=DB::table('leave_application as la')->select('la.*','c.name as category_name','r.name as role')->leftJoin('leave_category as c','c.id','=','la.category_id')->leftJoin('roles as r','r.id','=','la.role_id')->where('la.session_id',get_session_id());
        if(!empty($where)) $q->where($where);
        if($single) return $q->first() ? (array)$q->first() : null;
        return $q->orderBy('la.id','desc')->get();
    }

    private function dateCheck($daterange, Request $request)
    {
        $parts=explode(' - ',$daterange);
        if(count($parts)!=2) return 'Select all required field.';
        $start=date('Y-m-d',strtotime($parts[0])); $end=date('Y-m-d',strtotime($parts[1]));
        $today=date('Y-m-d');
        if($today==$start) return 'You can not leave the current day.';
        $applicant_id = $request->input('applicant_id') ? $request->input('applicant_id') : get_loggedin_user_id();
        $role_id = $request->input('user_role') ? $request->input('user_role') : loggedin_role_id();
        $leaves=DB::table('leave_application')->where('user_id',$applicant_id)->where('role_id',$role_id)->get();
        foreach($leaves as $leave){
            $dates=$this->userLeaveDays($leave->start_date,$leave->end_date);
            if(in_array($start,$dates) || in_array($end,$dates)) return 'Already have leave in the selected time.';
        }
        return true;
    }

    private function leaveCheck($type_id, Request $request)
    {
        $daterange=$request->input('daterange');
        if(empty($daterange)) return 'Select all required field.';
        $parts=explode(' - ',$daterange);
        $start=date('Y-m-d',strtotime($parts[0])); $end=date('Y-m-d',strtotime($parts[1]));
        $applicant_id = $request->input('applicant_id') ? $request->input('applicant_id') : get_loggedin_user_id();
        $role_id = $request->input('user_role') ? $request->input('user_role') : loggedin_role_id();
        $leave_total=DB::table('leave_category')->where('id',$type_id)->value('days');
        $total_spent=DB::table('leave_application')->where(['user_id'=>$applicant_id,'role_id'=>$role_id,'category_id'=>$type_id,'status'=>2])->sum('leave_days');
        $leave_days=(new \DateTime($start))->diff(new \DateTime($end))->format('%a')+1;
        $left=$leave_total-$total_spent;
        if($left < $leave_days) return "Applyed for $leave_days days, get maximum $left Days days.";
        return true;
    }

    private function uniqueCategoryCheck($name,$role_id,$branch_id,$excludeId=null)
    {
        $q=DB::table('leave_category')->where('name',$name)->where('role_id',$role_id)->where('branch_id',$branch_id);
        if($excludeId) $q->where('id','!=',$excludeId);
        return $q->count()==0;
    }

    private function userLeaveDays($start,$end)
    {
        $dates=[]; $current=strtotime($start); $end=strtotime($end);
        while($current<=$end){ $dates[]=date('Y-m-d',$current); $current=strtotime('+1 day',$current); }
        return $dates;
    }

    private function resolveBranchId(Request $request): int
    {
        if(is_superadmin_loggedin()){
            $bid=$request->input('branch_id');
            if(!empty($bid)) return (int)$bid;
            return (int)(get_loggedin_branch_id() ?? 0);
        }
        return (int)get_loggedin_branch_id();
    }
}
