<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\FeeAllocation;
use App\Models\FeeFine;
use App\Models\FeeGroup;
use App\Models\FeeGroupDetail;
use App\Models\FeePaymentHistory;
use App\Models\FeesReminder;
use App\Models\FeesType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class FeesController extends Controller
{
    private function branchId(Request $request): int
    {
        if (is_superadmin_loggedin()) {
            return (int) ($request->input('branch_id') ?: get_loggedin_branch_id() ?: $request->input('branch_id'));
        }
        return (int) get_loggedin_branch_id();
    }

    private function resolveBranchId(Request $request): int
    {
        if (is_superadmin_loggedin()) {
            $bid = $request->input('branch_id');
            if (!empty($bid)) return (int) $bid;
            return (int) get_loggedin_branch_id();
        }
        return (int) get_loggedin_branch_id();
    }

    // ---------- Helpers ported from Fees_model ----------
    public function feeFineCalculation($allocationID, $typeID)
    {
        $sessionID = get_session_id();
        $row = DB::table('fee_allocation as a')
            ->leftJoin('fee_groups_details as fd', function ($j) use ($typeID) {
                $j->on('fd.fee_groups_id', '=', 'a.group_id')->where('fd.fee_type_id', '=', $typeID);
            })
            ->join('fee_fine as f', function ($j) use ($sessionID) {
                $j->on('f.group_id', '=', 'fd.fee_groups_id')->on('f.type_id', '=', 'fd.fee_type_id')->where('f.session_id', '=', $sessionID);
            })
            ->where('a.id', $allocationID)
            ->select('fd.amount','fd.due_date','f.fine_value','f.fine_type','f.fee_frequency')
            ->first();
        if (!$row) return 0;
        $dueDate = $row->due_date;
        if (strtotime($dueDate) < strtotime(date('Y-m-d'))) {
            $feeAmount = $row->amount;
            $feeFrequency = $row->fee_frequency;
            $fineValue = $row->fine_value;
            $fineAmount = $row->fine_type == 1 ? $fineValue : ($feeAmount / 100) * $fineValue;
            $overDay = round((time() - strtotime($dueDate)) / (60*60*24));
            if ($feeFrequency != 0) {
                $fineAmount = ($overDay / $feeFrequency) * $fineAmount;
            }
            return $fineAmount;
        }
        return 0;
    }

    public function getBalance($allocationID, $typeID)
    {
        $bal = DB::selectOne("SELECT (SELECT gd.amount FROM fee_groups_details gd JOIN fee_allocation fa ON gd.fee_groups_id = fa.group_id WHERE fa.id = ? AND gd.fee_type_id = ?) as total_amount, (SELECT IFNULL(SUM(amount + discount),0) FROM fee_payment_history WHERE allocation_id=? AND type_id=?) as total_paid, (SELECT IFNULL(SUM(fine),0) FROM fee_payment_history WHERE allocation_id=? AND type_id=?) as total_fine", [$allocationID,$typeID,$allocationID,$typeID,$allocationID,$typeID]);
        $balance = ($bal->total_amount ?? 0) - ($bal->total_paid ?? 0);
        return ['balance'=>max(0,$balance), 'fine'=>$bal->total_fine ?? 0];
    }

    // ---------- Fees Type ----------
    public function type(Request $request)
    {
        abort_unless(get_permission('fees_type','is_view'),403);
        if ($request->isMethod('post')) {
            abort_unless(get_permission('fees_type','is_add'),403);
            $branchId = $this->resolveBranchId($request);
            $request->validate([
                'branch_id'=> is_superadmin_loggedin() ? ['required','integer','exists:branch,id'] : ['nullable'],
                'type_name'=> ['required','string','max:255', function($attr,$val,$fail) use($request,$branchId){
                    $q = FeesType::where('name',$val)->where('branch_id',$branchId);
                    if ($request->input('type_id')) $q->where('id','!=',$request->input('type_id'));
                    if ($q->exists()) $fail(translate('already_taken'));
                }],
                'description'=>['nullable','string'],
            ]);
            $data = ['branch_id'=>$branchId,'name'=>$request->input('type_name'),'fee_code'=>strtolower(str_replace(' ','-',$request->input('type_name'))),'description'=>$request->input('description','')];
            if ($request->input('type_id')) {
                FeesType::where('id',$request->input('type_id'))->update($data);
                set_alert('success', translate('information_has_been_updated_successfully'));
            } else {
                FeesType::create($data);
                set_alert('success', translate('information_has_been_saved_successfully'));
            }
            if ($request->expectsJson() || $request->ajax()) return response()->json(['status'=>'success']);
            return redirect()->route('fees.type');
        }
        $list = DB::table('fees_type as t')->leftJoin('branch as b','b.id','=','t.branch_id')->select('t.*','b.name as branch_name')->when(!is_superadmin_loggedin(), fn($q)=>$q->where('t.branch_id',get_loggedin_branch_id()))->orderBy('t.id')->get();
        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();
        return view('fees.type', ['title'=>translate('fees_type'),'categorylist'=>$list,'branches'=>$branches]);
    }

    public function typeEdit(Request $request, $id)
    {
        abort_unless(get_permission('fees_type','is_edit'),403);
        $row = FeesType::findOrFail($id);
        if ($request->isMethod('post')) {
            $branchId = $this->resolveBranchId($request);
            $request->validate([
                'type_name'=> ['required','string','max:255', function($attr,$val,$fail) use($request,$branchId,$id){
                    $q = FeesType::where('name',$val)->where('branch_id',$branchId)->where('id','!=',$id);
                    if ($q->exists()) $fail(translate('already_taken'));
                }],
                'description'=>['nullable','string'],
            ]);
            $row->update(['name'=>$request->input('type_name'),'fee_code'=>strtolower(str_replace(' ','-',$request->input('type_name'))),'description'=>$request->input('description','')]);
            set_alert('success', translate('information_has_been_updated_successfully'));
            if ($request->expectsJson() || $request->ajax()) return response()->json(['status'=>'success','url'=>route('fees.type')]);
            return redirect()->route('fees.type');
        }
        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();
        return view('fees.type_edit', ['title'=>translate('fees_type'),'category'=>$row,'branches'=>$branches]);
    }

    public function typeDelete(Request $request, $id)
    {
        abort_unless(get_permission('fees_type','is_delete'),403);
        $q = FeesType::where('id',$id);
        if (!is_superadmin_loggedin()) $q->where('branch_id',get_loggedin_branch_id());
        $q->delete();
        if ($request->expectsJson() || $request->ajax()) return response()->json(['status'=>'success']);
        return back();
    }

    // ---------- Fees Group ----------
    public function group(Request $request)
    {
        abort_unless(get_permission('fees_group','is_view'),403);
        if ($request->isMethod('post')) {
            abort_unless(get_permission('fees_group','is_add'),403);
            $branchId = $this->resolveBranchId($request);
            $request->validate([
                'branch_id'=> is_superadmin_loggedin() ? ['required'] : ['nullable'],
                'name'=>['required','string','max:255'],
                'description'=>['nullable','string'],
                'elem'=>['nullable','array'],
            ]);
            // validate each elem amount/due_date if fees_type_id set
            $elems = $request->input('elem',[]);
            $sel=0;
            foreach($elems as $k=>$v){
                if(isset($v['fees_type_id'])){
                    $sel++;
                    $request->validate(["elem.$k.due_date"=>['required'],"elem.$k.amount"=>['required','numeric','gt:0']]);
                }
            }
            if ($sel==0 && count($elems)>0) {
                // no type selected handled as legacy: still create group but warn? we mimic legacy set_alert error but still success
            }
            $group = FeeGroup::create(['name'=>$request->input('name'),'description'=>$request->input('description',''),'session_id'=>get_session_id(),'branch_id'=>$branchId]);
            foreach($elems as $row){
                if(isset($row['fees_type_id'])){
                    $exists = FeeGroupDetail::where('fee_groups_id',$group->id)->where('fee_type_id',$row['fees_type_id'])->exists();
                    if(!$exists){
                        FeeGroupDetail::create(['fee_groups_id'=>$group->id,'fee_type_id'=>$row['fees_type_id'],'due_date'=>date('Y-m-d',strtotime($row['due_date'])),'amount'=>$row['amount']]);
                    }
                }
            }
            set_alert('success', translate('information_has_been_saved_successfully'));
            if ($request->expectsJson() || $request->ajax()) return response()->json(['status'=>'success','url'=>route('fees.group')]);
            return redirect()->route('fees.group');
        }
        $list = DB::table('fee_groups as t')->leftJoin('branch as b','b.id','=','t.branch_id')->select('t.*','b.name as branch_name')->where('t.session_id',get_session_id())->when(!is_superadmin_loggedin(), fn($q)=>$q->where('t.branch_id',get_loggedin_branch_id()))->get();
        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();
        $feesTypes = FeesType::when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id',get_loggedin_branch_id()))->orderBy('name')->get();
        return view('fees.group', ['title'=>translate('fees_group'),'categorylist'=>$list,'branches'=>$branches,'feesTypes'=>$feesTypes]);
    }

    public function groupEdit(Request $request, $id)
    {
        abort_unless(get_permission('fees_group','is_edit'),403);
        $group = FeeGroup::findOrFail($id);
        if ($request->isMethod('post')) {
            $request->validate(['name'=>['required','string'],'description'=>['nullable']]);
            $elems = $request->input('elem',[]);
            $sel=[];
            foreach($elems as $k=>$v){
                if(isset($v['fees_type_id'])){
                    $sel[]=$v['fees_type_id'];
                    $request->validate(["elem.$k.due_date"=>['required'],"elem.$k.amount"=>['required','numeric','gt:0']]);
                }
            }
            $group->update(['name'=>$request->input('name'),'description'=>$request->input('description','')]);
            foreach($elems as $row){
                if(isset($row['fees_type_id'])){
                    $data=['fee_groups_id'=>$group->id,'fee_type_id'=>$row['fees_type_id'],'due_date'=>date('Y-m-d',strtotime($row['due_date'])),'amount'=>$row['amount']];
                    $q=FeeGroupDetail::where('fee_groups_id',$group->id)->where('fee_type_id',$row['fees_type_id'])->first();
                    if(!$q) FeeGroupDetail::create($data); else { $q->update($data); }
                }
            }
            if(count($sel)>0){
                FeeGroupDetail::where('fee_groups_id',$group->id)->whereNotIn('fee_type_id',$sel)->delete();
            } else {
                FeeGroupDetail::where('fee_groups_id',$group->id)->delete();
            }
            set_alert('success', translate('information_has_been_updated_successfully'));
            if ($request->expectsJson() || $request->ajax()) return response()->json(['status'=>'success','url'=>route('fees.group')]);
            return redirect()->route('fees.group');
        }
        $details = FeeGroupDetail::where('fee_groups_id',$id)->get();
        $feesTypes = FeesType::when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id',get_loggedin_branch_id()))->orderBy('name')->get();
        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();
        return view('fees.group_edit', ['title'=>translate('fees_group'),'group'=>$group,'details'=>$details,'feesTypes'=>$feesTypes,'branches'=>$branches]);
    }

    public function groupDelete(Request $request, $id)
    {
        abort_unless(get_permission('fees_group','is_delete'),403);
        $q=FeeGroup::where('id',$id);
        if(!is_superadmin_loggedin()) $q->where('branch_id',get_loggedin_branch_id());
        if($q->delete()){
            FeeGroupDetail::where('fee_groups_id',$id)->delete();
        }
        if ($request->expectsJson() || $request->ajax()) return response()->json(['status'=>'success']);
        return back();
    }

    // ---------- Fine ----------
    public function fine(Request $request)
    {
        // legacy uses fees_fine_setup permission but navigation uses fine
        if (!get_permission('fees_fine_setup','is_view') && !get_permission('fine','is_view')) abort(403);
        if ($request->isMethod('post')) {
            abort_unless(get_permission('fees_fine_setup','is_add') || get_permission('fine','is_add'),403);
            $branchId = $this->resolveBranchId($request);
            $request->validate([
                'branch_id'=> is_superadmin_loggedin()?['required']:['nullable'],
                'group_id'=>['required','integer','exists:fee_groups,id'],
                'fine_type_id'=>['required','integer','exists:fees_type,id', function($a,$v,$fail) use($request){
                    $g=$request->input('group_id'); $fid=$request->input('fine_id');
                    $q=FeeFine::where('group_id',$g)->where('type_id',$v);
                    if($fid) $q->where('id','!=',$fid);
                    if($q->exists()) $fail(translate('already_taken'));
                }],
                'fine_type'=>['required'],
                'fine_value'=>['required','numeric','gt:0'],
                'fee_frequency'=>['required'],
            ]);
            FeeFine::create(['group_id'=>$request->input('group_id'),'type_id'=>$request->input('fine_type_id'),'fine_value'=>$request->input('fine_value'),'fine_type'=>$request->input('fine_type'),'fee_frequency'=>$request->input('fee_frequency'),'branch_id'=>$branchId,'session_id'=>get_session_id()]);
            set_alert('success', translate('information_has_been_saved_successfully'));
            if ($request->expectsJson() || $request->ajax()) return response()->json(['status'=>'success']);
            return redirect()->route('fees.fine');
        }
        $list = DB::table('fee_fine as t')->leftJoin('branch as b','b.id','=','t.branch_id')->leftJoin('fee_groups as g','g.id','=','t.group_id')->leftJoin('fees_type as ft','ft.id','=','t.type_id')->select('t.*','b.name as branch_name','g.name as group_name','ft.name as type_name')->when(!is_superadmin_loggedin(), fn($q)=>$q->where('t.branch_id',get_loggedin_branch_id()))->get();
        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();
        $feeGroups = FeeGroup::when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id',get_loggedin_branch_id()))->where('session_id',get_session_id())->get();
        $feesTypes = FeesType::when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id',get_loggedin_branch_id()))->get();
        return view('fees.fine', ['title'=>translate('fine_setup'),'finelist'=>$list,'branches'=>$branches,'feeGroups'=>$feeGroups,'feesTypes'=>$feesTypes]);
    }

    public function fineEdit(Request $request, $id)
    {
        abort_unless(get_permission('fees_fine_setup','is_edit') || get_permission('fine','is_edit'),403);
        $fine = FeeFine::findOrFail($id);
        if ($request->isMethod('post')) {
            $branchId = $this->resolveBranchId($request);
            $request->validate([
                'group_id'=>['required','integer','exists:fee_groups,id'],
                'fine_type_id'=>['required','integer','exists:fees_type,id', function($a,$v,$fail) use($request,$id){
                    $q=FeeFine::where('group_id',$request->input('group_id'))->where('type_id',$v)->where('id','!=',$id);
                    if($q->exists()) $fail(translate('already_taken'));
                }],
                'fine_type'=>['required'],
                'fine_value'=>['required','numeric','gt:0'],
                'fee_frequency'=>['required'],
            ]);
            $fine->update(['group_id'=>$request->input('group_id'),'type_id'=>$request->input('fine_type_id'),'fine_value'=>$request->input('fine_value'),'fine_type'=>$request->input('fine_type'),'fee_frequency'=>$request->input('fee_frequency'),'branch_id'=>$branchId,'session_id'=>get_session_id()]);
            set_alert('success', translate('information_has_been_updated_successfully'));
            if ($request->expectsJson() || $request->ajax()) return response()->json(['status'=>'success','url'=>route('fees.fine')]);
            return redirect()->route('fees.fine');
        }
        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();
        $feeGroups = FeeGroup::when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id',get_loggedin_branch_id()))->where('session_id',get_session_id())->get();
        $feesTypes = FeesType::when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id',get_loggedin_branch_id()))->get();
        return view('fees.fine_edit', ['title'=>translate('fine_setup'),'fine'=>$fine,'branches'=>$branches,'feeGroups'=>$feeGroups,'feesTypes'=>$feesTypes]);
    }

    public function fineDelete(Request $request, $id)
    {
        abort_unless(get_permission('fees_fine_setup','is_delete') || get_permission('fine','is_delete'),403);
        $q=FeeFine::where('id',$id);
        if(!is_superadmin_loggedin()) $q->where('branch_id',get_loggedin_branch_id());
        $q->delete();
        if ($request->expectsJson() || $request->ajax()) return response()->json(['status'=>'success']);
        return back();
    }

    // ---------- Allocation ----------
    public function allocation(Request $request)
    {
        abort_unless(get_permission('fees_allocation','is_view') || get_permission('fees_allocation','is_add'),403);
        $branchId = $this->resolveBranchId($request);
        $studentlist = null;
        $class_id = $request->input('class_id');
        $section_id = $request->input('section_id');
        $fee_group_id = $request->input('fee_group_id') ?? $request->input('fee_groups_id');
        if ($request->has('search')) {
            $request->validate(['class_id'=>['required'],'section_id'=>['required'],'fee_group_id'=>['required','exists:fee_groups,id']]);
            $studentlist = $this->getStudentAllocationList($class_id,$section_id,$fee_group_id,$branchId);
        }
        if ($request->has('save')) {
            $student_array = $request->input('stu_operations',[]);
            $fee_groupID = $request->input('fee_group_id');
            foreach($student_array as $sid){
                $data=['student_id'=>$sid,'group_id'=>$fee_groupID,'session_id'=>get_session_id(),'branch_id'=>$branchId];
                if(!FeeAllocation::where($data)->exists()) FeeAllocation::create($data);
            }
            $delQ = FeeAllocation::where('group_id',$fee_groupID)->where('session_id',get_session_id());
            if(!empty($student_array)) $delQ->whereNotIn('student_id',$student_array);
            $delQ->delete();
            set_alert('success', translate('information_has_been_saved_successfully'));
            return redirect()->route('fees.allocation');
        }
        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();
        $feeGroups = FeeGroup::when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id',get_loggedin_branch_id()))->where('session_id',get_session_id())->get();
        return view('fees.allocation', ['title'=>translate('fees_allocation'),'branch_id'=>$branchId,'branches'=>$branches,'feeGroups'=>$feeGroups,'studentlist'=>$studentlist,'class_id'=>$class_id,'section_id'=>$section_id,'fee_group_id'=>$fee_group_id]);
    }

    private function getStudentAllocationList($classID, $sectionID, $groupID, $branchID)
    {
        $q = DB::table('enroll as e')
            ->leftJoin('student as s','s.id','=','e.student_id')
            ->leftJoin('login_credential as l', function($j){ $j->on('l.user_id','=','s.id')->where('l.role','=','7'); })
            ->leftJoin('fee_allocation as fa', function($j) use($groupID){ $j->on('fa.student_id','=','e.student_id')->where('fa.group_id','=',$groupID); })
            ->select('e.student_id','e.roll','e.class_id','e.section_id','s.photo','s.first_name','s.last_name','s.register_no','s.mobileno', DB::raw('IFNULL(fa.id,0) as allocation_id'), 'fa.group_id')
            ->where('e.class_id',$classID)->where('e.branch_id',$branchID)->where('l.active',1);
        if($sectionID!='all') $q->where('e.section_id',$sectionID);
        $q->orderBy('s.id');
        $res = $q->get();
        foreach($res as &$r){ $r->fullname = trim(($r->first_name??'').' '.($r->last_name??'')); }
        return $res;
    }

    // ---------- Collect / Invoice ----------
    public function collect(Request $request)
    {
        abort_unless(get_permission('collect_fees','is_view') || get_permission('fees_allocation','is_view'),403);
        $branchId = $this->resolveBranchId($request);
        $invoicelist = null;
        $class_id = $request->input('class_id');
        $section_id = $request->input('section_id');
        if ($request->has('search') || $request->input('search')) {
            $invoicelist = $this->getInvoiceList($class_id,$section_id,$branchId);
        }
        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();
        return view('fees.collect', ['title'=>translate('collect_fees'),'branch_id'=>$branchId,'branches'=>$branches,'invoicelist'=>$invoicelist,'class_id'=>$class_id,'section_id'=>$section_id]);
    }

    private function getInvoiceList($class_id,$section_id,$branch_id)
    {
        $studentIds = DB::table('enroll as e')->join('fee_allocation as fa','fa.student_id','=','e.student_id')->where('fa.branch_id',$branch_id)->where('e.class_id',$class_id)->when($section_id!='all', fn($q)=>$q->where('e.section_id',$section_id))->distinct()->pluck('e.student_id');
        if($studentIds->isEmpty()) return collect();
        $students = DB::table('enroll as e')->leftJoin('student as s','s.id','=','e.student_id')->leftJoin('class as c','c.id','=','e.class_id')->leftJoin('section as se','se.id','=','e.section_id')->whereIn('e.student_id',$studentIds)->where('e.class_id',$class_id)->when($section_id!='all', fn($q)=>$q->where('e.section_id',$section_id))->orderBy('e.roll')->select('e.student_id','e.roll','s.first_name','s.last_name','s.register_no','s.mobileno','c.name as class_name','se.name as section_name')->get();
        $groups = DB::table('fee_allocation as fa')->join('fee_groups as g','g.id','=','fa.group_id')->whereIn('fa.student_id',$studentIds)->select('fa.student_id','g.name as group_name')->get()->groupBy('student_id');
        foreach($students as $s){ $s->feegroup = $groups[$s->student_id] ?? collect(); }
        return $students;
    }

    public function invoice(Request $request, $id)
    {
        abort_unless(get_permission('invoice','is_view') || get_permission('collect_fees','is_view'),403);
        $invoice = $this->getInvoiceStatus($id);
        $basic = DB::table('enroll as e')->join('student as s','s.id','=','e.student_id')->join('class as c','c.id','=','e.class_id')->leftJoin('branch as b','b.id','=','e.branch_id')->where('e.student_id',$id)->select('s.id','e.branch_id','s.first_name','s.last_name','s.email as student_email','s.current_address as student_address','c.name as class_name','b.school_name','b.email as school_email','b.mobileno as school_mobileno','b.address as school_address')->first();
        // details
        $details = DB::table('fee_allocation as fa')->leftJoin('fee_groups_details as fgd','fgd.fee_groups_id','=','fa.group_id')->leftJoin('fees_type as ft','ft.id','=','fgd.fee_type_id')->where('fa.student_id',$id)->select('fa.id as allocation_id','ft.name','ft.id as fee_type_id','fgd.amount','fgd.due_date','fgd.fee_type_id')->get();
        // enrich with payment
        foreach($details as $d){
            $p = DB::table('fee_payment_history')->where('allocation_id',$d->allocation_id)->where('type_id',$d->fee_type_id)->select(DB::raw('IFNULL(SUM(amount),0) as total_amount'),DB::raw('IFNULL(SUM(discount),0) as total_discount'),DB::raw('IFNULL(SUM(fine),0) as total_fine'))->first();
            $d->paid = $p->total_amount ?? 0;
            $d->discount = $p->total_discount ?? 0;
            $d->fine_paid = $p->total_fine ?? 0;
            $d->fine_due = max(0, $this->feeFineCalculation($d->allocation_id,$d->fee_type_id) - $d->fine_paid);
            $d->balance = max(0, $d->amount - ($p->total_amount + $p->total_discount));
        }
        $accounts = DB::table('accounts')->when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id',get_loggedin_branch_id()))->get();
        $payTypes = DB::table('payment_types')->get();
        return view('fees.invoice', ['title'=>translate('invoice'),'invoice'=>$invoice,'basic'=>$basic,'details'=>$details,'student_id'=>$id,'accounts'=>$accounts,'payTypes'=>$payTypes]);
    }

    private function getInvoiceStatus($studentID)
    {
        $sess=get_session_id();
        $r = DB::selectOne("SELECT (SELECT COUNT(*) FROM fee_allocation WHERE student_id=? AND session_id=?) as has_allocation, (SELECT IFNULL(SUM(gd.amount),0) FROM fee_allocation fa INNER JOIN fee_groups_details gd ON gd.fee_groups_id=fa.group_id WHERE fa.student_id=?) as total_fees, (SELECT IFNULL(SUM(h.amount+h.discount),0) FROM fee_payment_history h INNER JOIN fee_allocation a ON h.allocation_id=a.id WHERE a.student_id=? AND a.session_id=?) as total_paid, (SELECT MIN(id) FROM fee_allocation WHERE student_id=? AND session_id=?) as min_allocation_id", [$studentID,$sess,$studentID,$studentID,$sess,$studentID,$sess]);
        $status='unpaid';
        if($r->has_allocation==0) $status='no_allocation';
        elseif($r->total_fees>0 && $r->total_paid >= $r->total_fees) $status='total';
        elseif($r->total_paid>0) $status='partly';
        return ['status'=>$status,'invoice_no'=>str_pad($r->min_allocation_id??0,4,'0',STR_PAD_LEFT)];
    }

    public function feeAdd(Request $request)
    {
        abort_unless(get_permission('collect_fees','is_add'),403);
        $request->validate([
            'fees_type'=>['required'],
            'date'=>['required','date'],
            'amount'=>['required','numeric','gt:0', function($a,$v,$fail) use($request){
                $type = explode('|',$request->input('fees_type'));
                if(count($type)!=2) return;
                $b=$this->getBalance($type[0],$type[1]);
                $discount = $request->input('discount_amount',0);
                if($b['balance'] < ($v+$discount)) $fail('Amount cannot be greater than the remaining.');
            }],
            'pay_via'=>['required'],
        ]);
        $feesType = explode('|',$request->input('fees_type'));
        $arr = ['allocation_id'=>$feesType[0],'type_id'=>$feesType[1],'collect_by'=>get_loggedin_user_id(),'amount'=>$request->input('amount'),'discount'=>$request->input('discount_amount',0),'fine'=>$request->input('fine_amount',0),'pay_via'=>$request->input('pay_via'),'remarks'=>$request->input('remarks',''),'date'=>$request->input('date')];
        FeePaymentHistory::create($arr);
        if ($request->has('account_id') && $request->input('account_id')) {
            $this->saveTransaction(['account_id'=>$request->input('account_id'),'amount'=>($arr['amount']+$arr['fine'])-$arr['discount'],'date'=>$arr['date']]);
        }
        set_alert('success', translate('information_has_been_saved_successfully'));
        if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success']);
        return back();
    }

    public function getBalanceByType(Request $request)
    {
        $input=$request->input('typeID');
        if(empty($input)) return response()->json(['balance'=>0,'fine'=>0]);
        $feesType=explode('|',$input);
        $fine=$this->feeFineCalculation($feesType[0],$feesType[1]);
        $b=$this->getBalance($feesType[0],$feesType[1]);
        $balance=$b['balance'];
        $fine=abs($fine - $b['fine']);
        return response()->json(['balance'=>$balance,'fine'=>$fine]);
    }

    public function getTypeByBranch(Request $request)
    {
        $branchID = $this->resolveBranchId($request);
        $typeID = $request->input('type_id',0);
        $html='';
        if(!empty($branchID)){
            $groups = FeeGroup::where('session_id',get_session_id())->where('branch_id',$branchID)->get();
            if($groups->count()){
                $html.="<option value=''>".translate('select')."</option>";
                foreach($groups as $row){
                    $html.='<optgroup label="'.$row->name.'">';
                    $details = FeeGroupDetail::where('fee_groups_id',$row->id)->get();
                    foreach($details as $t){
                        $sel=($t->fee_groups_id."|".$t->fee_type_id == $typeID ? 'selected':'');
                        $name = FeesType::where('id',$t->fee_type_id)->value('name') ?? '';
                        $html.='<option value="'.$t->fee_groups_id."|".$t->fee_type_id.'"' .$sel.'>'.$name.'</option>';
                    }
                    $html.='</optgroup>';
                }
            } else $html.='<option value="">'.translate('no_information_available').'</option>';
        } else $html.='<option value="">'.translate('select_branch_first').'</option>';
        return response($html);
    }

    public function getGroupByBranch(Request $request)
    {
        $branchId=$this->resolveBranchId($request);
        $html='';
        if(!empty($branchId)){
            $result=FeeGroup::where('branch_id',$branchId)->where('session_id',get_session_id())->get(['id','name']);
            if($result->count()){
                $html.="<option value=''>".translate('select')."</option>";
                foreach($result as $row) $html.='<option value="'.$row->id.'">'.$row->name.'</option>';
            } else $html.='<option value="">'.translate('no_information_available').'</option>';
        } else $html.='<option value="">'.translate('select_branch_first').'</option>';
        return response($html);
    }

    public function getTypeByGroup(Request $request)
    {
        $groupID=$request->input('group_id');
        $typeID=$request->input('type_id',0);
        $html='';
        if(!empty($groupID)){
            $result=DB::table('fee_groups_details as gd')->join('fees_type as t','t.id','=','gd.fee_type_id')->where('gd.fee_groups_id',$groupID)->select('t.id','t.name')->get();
            if($result->count()){
                $html.="<option value=''>".translate('select')."</option>";
                foreach($result as $row){ $sel=($row->id==$typeID?'selected':''); $html.='<option value="'.$row->id.'" '.$sel.'>'.$row->name.'</option>'; }
            } else $html.='<option value="">'.translate('no_information_available').'</option>';
        } else $html.='<option value="">'.translate('first_select_the_group').'</option>';
        return response($html);
    }

    // ---------- Reminder ----------
    public function reminder(Request $request)
    {
        abort_unless(get_permission('fees_reminder','is_view'),403);
        if($request->isMethod('post')){
            abort_unless(get_permission('fees_reminder','is_add'),403);
            $request->validate(['frequency'=>['required'],'days'=>['required','numeric'],'message'=>['required']]);
            if(is_superadmin_loggedin()) $request->validate(['branch_id'=>['required']]);
            $branchId=$this->resolveBranchId($request);
            FeesReminder::create(['frequency'=>$request->input('frequency'),'days'=>$request->input('days'),'message'=>$request->input('message'),'student'=>$request->has('chk_student')?1:0,'guardian'=>$request->has('chk_guardian')?1:0,'branch_id'=>$branchId]);
            set_alert('success', translate('information_has_been_saved_successfully'));
            if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success']);
            return redirect()->route('fees.reminder');
        }
        $list = DB::table('fees_reminder as t')->leftJoin('branch as b','b.id','=','t.branch_id')->select('t.*','b.name as branch_name')->when(!is_superadmin_loggedin(), fn($q)=>$q->where('t.branch_id',get_loggedin_branch_id()))->get();
        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();
        return view('fees.reminder', ['title'=>translate('fees_reminder'),'reminderlist'=>$list,'branches'=>$branches]);
    }

    public function editReminder(Request $request, $id)
    {
        abort_unless(get_permission('fees_reminder','is_edit'),403);
        $row=FeesReminder::findOrFail($id);
        if($request->isMethod('post')){
            $request->validate(['frequency'=>['required'],'days'=>['required','numeric'],'message'=>['required']]);
            $branchId=$this->resolveBranchId($request);
            $row->update(['frequency'=>$request->input('frequency'),'days'=>$request->input('days'),'message'=>$request->input('message'),'student'=>$request->has('chk_student')?1:0,'guardian'=>$request->has('chk_guardian')?1:0,'branch_id'=>$branchId]);
            set_alert('success', translate('information_has_been_updated_successfully'));
            if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success','url'=>route('fees.reminder')]);
            return redirect()->route('fees.reminder');
        }
        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();
        return view('fees.reminder_edit', ['title'=>translate('fees_reminder'),'reminder'=>$row,'branches'=>$branches]);
    }

    public function reminderDelete(Request $request,$id){
        abort_unless(get_permission('fees_reminder','is_delete'),403);
        $q=FeesReminder::where('id',$id);
        if(!is_superadmin_loggedin()) $q->where('branch_id',get_loggedin_branch_id());
        $q->delete();
        if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success']);
        return back();
    }

    // ---------- Reports ----------
    public function dueReport(Request $request){
        $branchId=$this->resolveBranchId($request);
        $invoicelist=null;
        if($request->has('search')){
            $class_id=$request->input('class_id');
            $section_id=$request->input('section_id');
            $invoicelist = DB::table('fee_allocation as fa')->leftJoin('fee_groups_details as gd','gd.fee_groups_id','=','fa.group_id')->leftJoin('enroll as e','e.student_id','=','fa.student_id')->leftJoin('student as s','s.id','=','e.student_id')->leftJoin('class as c','c.id','=','e.class_id')->leftJoin('section as se','se.id','=','e.section_id')->where('fa.session_id',get_session_id())->where('e.class_id',$class_id)->when($section_id!='all' && $section_id, fn($q)=>$q->where('e.section_id',$section_id))->groupBy('fa.student_id')->select('fa.id as allocation_id', DB::raw('sum(gd.amount) as total_fees'),'e.student_id','e.roll','s.first_name','s.last_name','s.register_no','s.mobileno','c.name as class_name','se.name as section_name')->orderBy('e.roll')->get();
            foreach($invoicelist as $k=>$v){
                $pay = DB::table('fee_allocation')->leftJoin('fee_payment_history','fee_payment_history.allocation_id','=','fee_allocation.id')->where('fee_allocation.student_id',$v->student_id)->select(DB::raw('IFNULL(SUM(amount),0) as total_paid'),DB::raw('IFNULL(SUM(discount),0) as total_discount'),DB::raw('IFNULL(SUM(fine),0) as total_fine'))->first();
                $v->payment = (array)$pay;
            }
        }
        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();
        return view('fees.due_report', ['title'=>translate('due_fees_report'),'branch_id'=>$branchId,'branches'=>$branches,'invoicelist'=>$invoicelist]);
    }

    // master view alias to group for navigation
    public function master(Request $request){ return $this->group($request); }

    private function saveTransaction($data)
    {
        try{
            $branchID = $this->resolveBranchId(request());
            $account = DB::table('accounts')->where('id',$data['account_id'])->first();
            if(!$account) return false;
            $newBalance = $account->balance + $data['amount'];
            $vh = ['name'=>'Student Fees Collection','type'=>'income','system'=>1,'branch_id'=>$branchID];
            $vhRow = DB::table('voucher_head')->where($vh)->first();
            if($vhRow) $vhId=$vhRow->id; else { DB::table('voucher_head')->insert($vh); $vhId=DB::getPdo()->lastInsertId(); }
            $trans = ['account_id'=>$data['account_id'],'voucher_head_id'=>$vhId,'type'=>'income','amount'=>$data['amount'],'date'=>$data['date'],'balance'=>$newBalance,'branch_id'=>$branchID];
            DB::table('transactions')->insert($trans);
            DB::table('accounts')->where('id',$data['account_id'])->update(['balance'=>$newBalance]);
            return true;
        } catch(\Throwable $e){ return false; }
    }
}
