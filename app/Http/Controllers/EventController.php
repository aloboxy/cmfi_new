<?php
namespace App\Http\Controllers;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class EventController extends Controller
{
    private function resolveBranchId(Request $request): ?int
    {
        if(is_superadmin_loggedin()){ $bid=$request->input('branch_id'); if($bid) return (int)$bid; return (int)(DB::table('branch')->value('id') ?? 0);}
        return (int)get_loggedin_branch_id();
    }
    public function index(Request $request)
    {
        abort_unless(get_permission('event','is_view'),403);
        if($request->isMethod('post')){
            $rules=['title'=>['required'],'audition'=>['required'],'daterange'=>['required']];
            if(is_superadmin_loggedin()) $rules['branch_id']=['required'];
            if(!$request->has('holiday')) $rules['type_id']=['required'];
            $audition=$request->input('audition');
            if($audition==2 || $audition==3) $rules['selected_audience']=['required','array'];
            $validated=$request->validate($rules);
            $branchId=$this->resolveBranchId($request);
            $selectedList=null;
            if($audition!=1) $selectedList=json_encode($request->input('selected_audience'));
            $type=$request->has('holiday')?'holiday':$request->input('type_id');
            $daterange=explode(' - ', $request->input('daterange'));
            $start=date("Y-m-d",strtotime($daterange[0])); $end=date("Y-m-d",strtotime($daterange[1]));
            DB::table('event')->insert(['branch_id'=>$branchId,'title'=>$validated['title'],'created_by'=>get_loggedin_user_id(),'remark'=>$request->input('remarks',''),'type'=>$type,'audition'=>$audition,'selected_list'=>$selectedList,'start_date'=>$start,'end_date'=>$end,'status'=>1]);
            set_alert('success', translate('information_has_been_updated_successfully'));
            if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success','url'=>route('events.index')]);
            return redirect()->route('events.index');
        }
        $branchId=$this->resolveBranchId($request);
        $branches=is_superadmin_loggedin()?Branch::orderBy('name')->get():collect();
        $events=DB::table('event')->when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id', get_loggedin_branch_id()))->orderBy('id','desc')->get();
        $types=DB::table('event_types')->when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id', get_loggedin_branch_id()))->get();
        $classes=$branchId?DB::table('class')->where('branch_id',$branchId)->get():collect();
        return view('event.index',['title'=>translate('events'),'branch_id'=>$branchId,'branches'=>$branches,'events'=>$events,'types'=>$types,'classes'=>$classes]);
    }
    public function destroy(Request $request,int $id)
    {
        abort_unless(get_permission('event','is_delete'),403);
        $row=DB::table('event')->where('id',$id)->first(); abort_if(!$row,404);
        if($row->created_by != get_loggedin_user_id()){ set_alert('error','You do not have permission to delete'); return back(); }
        DB::table('event')->where('id',$id)->delete();
        if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success']);
        set_alert('success', translate('information_has_been_deleted_successfully'));
        return back();
    }
    public function types(Request $request)
    {
        abort_unless(get_permission('event_type','is_view'),403);
        $branches=is_superadmin_loggedin()?Branch::orderBy('name')->get():collect();
        $typelist=DB::table('event_types as t')->leftJoin('branch as b','b.id','=','t.branch_id')->select('t.*','b.name as branch_name')->when(!is_superadmin_loggedin(), fn($q)=>$q->where('t.branch_id', get_loggedin_branch_id()))->get();
        return view('event.types',['title'=>translate('event_type'),'typelist'=>$typelist,'branches'=>$branches]);
    }
    public function typeStore(Request $request)
    {
        abort_unless(get_permission('event_type','is_add'),403);
        $rules=['type_name'=>['required','string','max:255']];
        if(is_superadmin_loggedin()) $rules['branch_id']=['required'];
        $validated=$request->validate($rules);
        $branchId=$this->resolveBranchId($request);
        $exists=DB::table('event_types')->where('name',$validated['type_name'])->where('branch_id',$branchId)->exists();
        if($exists) return back()->withErrors(['type_name'=>translate('already_taken')])->withInput();
        DB::table('event_types')->insert(['name'=>$validated['type_name'],'icon'=>$request->input('event_icon',''),'branch_id'=>$branchId]);
        set_alert('success', translate('information_has_been_saved_successfully'));
        return redirect()->route('events.type');
    }
    public function typeEdit(Request $request)
    {
        abort_unless(get_permission('event_type','is_edit'),403);
        $rules=['type_id'=>['required','integer','exists:event_types,id'],'type_name'=>['required','string']];
        if(is_superadmin_loggedin()) $rules['branch_id']=['required'];
        $validated=$request->validate($rules);
        $branchId=$this->resolveBranchId($request);
        $exists=DB::table('event_types')->where('name',$validated['type_name'])->where('branch_id',$branchId)->where('id','!=',$validated['type_id'])->exists();
        if($exists){
            if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'fail','error'=>['type_name'=>translate('already_taken')]]);
            return back()->withErrors(['type_name'=>translate('already_taken')]);
        }
        DB::table('event_types')->where('id',$validated['type_id'])->when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id', get_loggedin_branch_id()))->update(['name'=>$validated['type_name'],'icon'=>$request->input('event_icon',''),'branch_id'=>$branchId]);
        set_alert('success', translate('information_has_been_updated_successfully'));
        if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success','url'=>route('events.type')]);
        return redirect()->route('events.type');
    }
    public function typeDelete(Request $request,int $id)
    {
        abort_unless(get_permission('event_type','is_delete'),403);
        $q=DB::table('event_types')->where('id',$id);
        if(!is_superadmin_loggedin()) $q->where('branch_id', get_loggedin_branch_id());
        $q->delete();
        if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success']);
        return back();
    }
    public function status(Request $request)
    {
        $id=$request->input('id'); $status=$request->input('status')=='true'?1:0;
        $q=DB::table('event')->where('id',$id);
        if(!is_superadmin_loggedin()) $q->where('branch_id', get_loggedin_branch_id());
        $q->update(['status'=>$status]);
        return response()->json(['msg'=>translate('information_has_been_updated_successfully'),'status'=>true]);
    }
    public function getDetails(Request $request)
    {
        $id=$request->input('event_id');
        $auditions=["1"=>"everybody","2"=>"class","3"=>"section"];
        $ev=DB::table('event')->when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id', get_loggedin_branch_id()))->where('id',$id)->first();
        abort_if(!$ev,404);
        $type=$ev->type=='holiday'?translate('holiday'):get_type_name_by_id('event_types',$ev->type);
        $remark=empty($ev->remark)?'N/A':$ev->remark;
        $html="<tbody><tr><td>".translate('title')."</td><td>".e($ev->title)."</td></tr><tr><td>".translate('type')."</td><td>".e($type)."</td></tr><tr><td>".translate('date_of_start')."</td><td>"._d($ev->start_date)."</td></tr><tr><td>".translate('date_of_end')."</td><td>"._d($ev->end_date)."</td></tr><tr><td>".translate('audience')."</td><td>".translate($auditions[$ev->audition]);
        if($ev->audition!=1){
            $selecteds=json_decode($ev->selected_list);
            if(is_array($selecteds)) foreach($selecteds as $sel){ $name=DB::table($auditions[$ev->audition])->where('id',$sel)->value('name'); $html.="<br> <small> - ".e($name)."</small>"; }
        }
        $html.="</td></tr><tr><td>".translate('description')."</td><td>".e($remark)."</td></tr></tbody>";
        return response($html);
    }
    public function getSectionByBranch(Request $request)
    {
        $branchId=$this->resolveBranchId($request);
        $result=DB::table('class')->where('branch_id',$branchId)->get();
        $html='';
        foreach($result as $class){
            $html.='<optgroup label="'.e($class->name).'">';
            $allocations=DB::table('sections_allocation')->where('class_id',$class->id)->get();
            foreach($allocations as $allocation){
                $section=DB::table('section')->where('id',$allocation->section_id)->first();
                if($section) $html.='<option value="'.$allocation->section_id.'">'.e($section->name).'</option>';
            }
            $html.='</optgroup>';
        }
        return response($html);
    }
    public function getEventsList(Request $request,int $branchID=0)
    {
        if(!is_superadmin_loggedin()) $branchID=get_loggedin_branch_id();
        $events=DB::table('event')->where('branch_id',$branchID)->where('status',1)->get();
        $data=[];
        foreach($events as $row){
            $item=['id'=>$row->id,'title'=>$row->title,'start'=>$row->start_date,'end'=>date('Y-m-d', strtotime($row->end_date."+1 days"))];
            if($row->type=='holiday'){ $item['className']='fc-event-danger'; $item['icon']='umbrella-beach'; } else { $item['icon']=get_type_name_by_id('event_types',$row->type,'icon'); }
            $data[]=$item;
        }
        return response()->json($data);
    }
}
