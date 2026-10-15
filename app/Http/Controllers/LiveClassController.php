<?php
namespace App\Http\Controllers;
use App\Models\Branch;
use App\Models\LiveClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class LiveClassController extends Controller
{
    private function resolveBranchId(Request $request): ?int
    {
        if(is_superadmin_loggedin()){ $bid=$request->input('branch_id'); if($bid) return (int)$bid; return (int)(DB::table('branch')->value('id') ?? 0);}
        return (int)get_loggedin_branch_id();
    }
    public function index(Request $request)
    {
        abort_unless(get_permission('live_class','is_view'),403);
        $branchId=$this->resolveBranchId($request);
        $branches=is_superadmin_loggedin()?Branch::orderBy('name')->get():collect();
        $query=DB::table('live_class')->leftJoin('branch','branch.id','=','live_class.branch_id')->leftJoin('class','class.id','=','live_class.class_id')->leftJoin('staff','staff.id','=','live_class.created_by')
            ->select('live_class.*','class.name as class_name','staff.name as staffname','branch.name as branchname');
        if(!is_superadmin_loggedin() && !is_admin_loggedin()){
            $query->where('live_class.created_by', get_loggedin_user_id());
        } elseif(!empty($branchId)){
            // optionally filter? legacy shows all for superadmin
        }
        $liveClass=$query->orderBy('live_class.id','asc')->get()->map(function($r){ $secs=json_decode($r->section_id,true); $names=''; if(is_array($secs)){ foreach($secs as $s){ $names.=get_type_name_by_id('section',$s).'<br>'; } } $r->section_details=$names; return $r; });
        $classes=$branchId?DB::table('class')->where('branch_id',$branchId)->get():collect();
        return view('live_class.index',['title'=>translate('live_class_rooms'),'liveClass'=>$liveClass,'branches'=>$branches,'branch_id'=>$branchId,'classes'=>$classes]);
    }
    public function store(Request $request)
    {
        abort_unless(get_permission('live_class','is_add'),403);
        $rules=['title'=>['required'],'zoom_meeting_id'=>['required'],'zoom_meeting_password'=>['required'],'class_id'=>['required','integer'],'section'=>['required','array'],'date'=>['required','date'],'time_start'=>['required'],'time_end'=>['required']];
        if(is_superadmin_loggedin()) $rules['branch_id']=['required','integer','exists:branch,id'];
        $validated=$request->validate($rules);
        $branchId=$this->resolveBranchId($request);
        LiveClass::create(['branch_id'=>$branchId,'title'=>$validated['title'],'meeting_id'=>$validated['zoom_meeting_id'],'meeting_password'=>$validated['zoom_meeting_password'],'class_id'=>$validated['class_id'],'section_id'=>json_encode($validated['section']),'remarks'=>$request->input('remarks',''),'date'=>date("Y-m-d",strtotime($validated['date'])),'start_time'=>date("H:i",strtotime($validated['time_start'])),'end_time'=>date("H:i",strtotime($validated['time_end'])),'created_by'=>get_loggedin_user_id()]);
        set_alert('success', translate('information_has_been_saved_successfully'));
        if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success']);
        return redirect()->route('liveclass.index');
    }
    public function edit(int $id)
    {
        abort_unless(get_permission('live_class','is_edit'),403);
        $live=LiveClass::where('id',$id)->firstOrFail();
        $branchId=$live->branch_id;
        $branches=is_superadmin_loggedin()?Branch::orderBy('name')->get():collect();
        $classes=DB::table('class')->where('branch_id',$branchId)->get();
        $sections=DB::table('sections_allocation')->join('section','section.id','=','sections_allocation.section_id')->where('sections_allocation.class_id',$live->class_id)->select('section.id','section.name')->get();
        return view('live_class.edit',['title'=>translate('live_class'),'live'=>$live,'branches'=>$branches,'classes'=>$classes,'sections'=>$sections]);
    }
    public function update(Request $request,int $id)
    {
        abort_unless(get_permission('live_class','is_edit'),403);
        $rules=['title'=>['required'],'zoom_meeting_id'=>['required'],'zoom_meeting_password'=>['required'],'class_id'=>['required','integer'],'section'=>['required','array'],'date'=>['required','date'],'time_start'=>['required'],'time_end'=>['required']];
        if(is_superadmin_loggedin()) $rules['branch_id']=['required','integer','exists:branch,id'];
        $validated=$request->validate($rules);
        $live=LiveClass::where('id',$id)->firstOrFail();
        $branchId=is_superadmin_loggedin()? (int)$request->input('branch_id',$live->branch_id) : $live->branch_id;
        $live->update(['branch_id'=>$branchId,'title'=>$validated['title'],'meeting_id'=>$validated['zoom_meeting_id'],'meeting_password'=>$validated['zoom_meeting_password'],'class_id'=>$validated['class_id'],'section_id'=>json_encode($validated['section']),'remarks'=>$request->input('remarks',''),'date'=>date("Y-m-d",strtotime($validated['date'])),'start_time'=>date("H:i",strtotime($validated['time_start'])),'end_time'=>date("H:i",strtotime($validated['time_end']))]);
        set_alert('success', translate('information_has_been_updated_successfully'));
        if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success','url'=>route('liveclass.index')]);
        return redirect()->route('liveclass.index');
    }
    public function destroy(Request $request,int $id)
    {
        abort_unless(get_permission('live_class','is_delete'),403);
        $q=LiveClass::where('id',$id);
        if(!is_superadmin_loggedin()) $q->where('branch_id', get_loggedin_branch_id());
        $q->delete();
        if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success']);
        set_alert('success', translate('information_has_been_deleted_successfully'));
        return back();
    }
    public function host()
    {
        abort_unless(get_permission('live_class','is_add'),403);
        return view('live_class.host');
    }
}
