<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Hostel;
use App\Models\HostelCategory;
use App\Models\HostelRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HostelController extends Controller
{
    private function branchId(Request $r): int { return is_superadmin_loggedin()? (int)($r->input('branch_id')?:get_loggedin_branch_id()): (int)get_loggedin_branch_id(); }

    public function index(Request $request)
    {
        abort_unless(get_permission('hostel','is_view'),403);
        if($request->isMethod('post')){
            abort_unless(get_permission('hostel','is_add'),403);
            if(is_superadmin_loggedin()) $request->validate(['branch_id'=>['required']]);
            $request->validate(['name'=>['required'],'category_id'=>['required'],'watchman_name'=>['required']]);
            Hostel::create(['name'=>$request->input('name'),'category_id'=>$request->input('category_id'),'address'=>$request->input('hostel_address',''),'watchman'=>$request->input('watchman_name'),'remarks'=>$request->input('remarks',''),'branch_id'=>$this->branchId($request)]);
            set_alert('success', translate('information_has_been_saved_successfully'));
            if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success','url'=>route('hostels.index')]);
            return redirect()->route('hostels.index');
        }
        $list=DB::table('hostel as t')->leftJoin('branch as b','b.id','=','t.branch_id')->leftJoin('hostel_category as c','c.id','=','t.category_id')->select('t.*','b.name as branch_name','c.name as category_name')->when(!is_superadmin_loggedin(), fn($q)=>$q->where('t.branch_id',get_loggedin_branch_id()))->orderBy('t.id')->get();
        $branches=is_superadmin_loggedin()? Branch::orderBy('name')->get():collect();
        $categories=HostelCategory::when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id',get_loggedin_branch_id()))->get();
        return view('hostels.index', ['title'=>translate('hostel_master'),'hostellist'=>$list,'branches'=>$branches,'categories'=>$categories]);
    }

    public function edit(Request $request,$id)
    {
        abort_unless(get_permission('hostel','is_edit'),403);
        $hostel=Hostel::findOrFail($id);
        if($request->isMethod('post')){
            $request->validate(['name'=>['required'],'category_id'=>['required'],'watchman_name'=>['required']]);
            $hostel->update(['name'=>$request->input('name'),'category_id'=>$request->input('category_id'),'address'=>$request->input('hostel_address',''),'watchman'=>$request->input('watchman_name'),'remarks'=>$request->input('remarks',''),'branch_id'=> $this->branchId($request) ?: $hostel->branch_id]);
            set_alert('success', translate('information_has_been_updated_successfully'));
            if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success','url'=>route('hostels.index')]);
            return redirect()->route('hostels.index');
        }
        $categories=HostelCategory::when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id',get_loggedin_branch_id()))->get();
        return view('hostels.edit', ['title'=>translate('hostel_master'),'hostel'=>$hostel,'categories'=>$categories]);
    }

    public function delete(Request $request,$id){
        abort_unless(get_permission('hostel','is_delete'),403);
        $q=Hostel::where('id',$id);
        if(!is_superadmin_loggedin()) $q->where('branch_id',get_loggedin_branch_id());
        $q->delete();
        if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success']);
        return back();
    }

    public function category(Request $request)
    {
        if($request->isMethod('post') && $request->has('save')){
            abort_unless(get_permission('hostel_category','is_add'),403);
            if(is_superadmin_loggedin()) $request->validate(['branch_id'=>['required']]);
            $request->validate(['category_name'=>['required', function($a,$v,$fail) use($request){
                $branchId = $this->branchId($request);
                $q=HostelCategory::where('name',$v)->where('type',$request->input('type'))->where('branch_id',$branchId);
                if($request->input('category_id')) $q->where('id','!=',$request->input('category_id'));
                if($q->exists()) $fail(translate('already_taken'));
            }],'type'=>['required']]);
            $data=['name'=>$request->input('category_name'),'type'=>$request->input('type'),'description'=>$request->input('description',''),'branch_id'=>$this->branchId($request)];
            if($request->input('category_id')) HostelCategory::where('id',$request->input('category_id'))->update($data);
            else HostelCategory::create($data);
            set_alert('success', translate('information_has_been_saved_successfully'));
            return redirect()->route('hostels.category');
        }
        $list=DB::table('hostel_category as t')->leftJoin('branch as b','b.id','=','t.branch_id')->select('t.*','b.name as branch_name')->when(!is_superadmin_loggedin(), fn($q)=>$q->where('t.branch_id',get_loggedin_branch_id()))->orderBy('t.id')->get();
        $branches=is_superadmin_loggedin()? Branch::orderBy('name')->get():collect();
        return view('hostels.category', ['title'=>translate('category'),'categorylist'=>$list,'branches'=>$branches]);
    }

    public function categoryEdit(Request $request)
    {
        abort_unless(get_permission('hostel_category','is_edit'),403);
        if(is_superadmin_loggedin()) $request->validate(['branch_id'=>['required']]);
        $request->validate(['category_name'=>['required', function($a,$v,$fail) use($request){
            $branchId=$this->branchId($request);
            $q=HostelCategory::where('name',$v)->where('type',$request->input('type'))->where('branch_id',$branchId)->where('id','!=',$request->input('category_id'));
            if($q->exists()) $fail(translate('already_taken'));
        }],'type'=>['required']]);
        HostelCategory::where('id',$request->input('category_id'))->update(['name'=>$request->input('category_name'),'type'=>$request->input('type'),'description'=>$request->input('description',''),'branch_id'=>$this->branchId($request)]);
        set_alert('success', translate('information_has_been_updated_successfully'));
        return response()->json(['status'=>'success','url'=>route('hostels.category')]);
    }

    public function categoryDelete(Request $request,$id){
        abort_unless(get_permission('hostel_category','is_delete'),403);
        $q=HostelCategory::where('id',$id);
        if(!is_superadmin_loggedin()) $q->where('branch_id',get_loggedin_branch_id());
        $q->delete();
        if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success']);
        return back();
    }

    public function rooms(Request $request)
    {
        abort_unless(get_permission('room','is_view') || get_permission('hostel_room','is_view'),403);
        if($request->isMethod('post')){
            abort_unless(get_permission('hostel_room','is_add') || get_permission('room','is_add'),403);
            if(is_superadmin_loggedin()) $request->validate(['branch_id'=>['required']]);
            $request->validate(['name'=>['required', function($a,$v,$fail) use($request){
                $branchId=$this->branchId($request);
                $q=HostelRoom::where('name',$v)->where('branch_id',$branchId);
                if($request->input('room_id')) $q->where('id','!=',$request->input('room_id'));
                if($q->exists()) $fail(translate('already_taken'));
            }],'hostel_id'=>['required'],'category_id'=>['required'],'number_of_beds'=>['required','numeric'],'bed_fee'=>['required','numeric']]);
            $data=['name'=>$request->input('name'),'hostel_id'=>$request->input('hostel_id'),'category_id'=>$request->input('category_id'),'no_beds'=>$request->input('number_of_beds'),'bed_fee'=>$request->input('bed_fee'),'remarks'=>$request->input('remarks',''),'branch_id'=>$this->branchId($request)];
            if($request->input('room_id')) HostelRoom::where('id',$request->input('room_id'))->update($data);
            else HostelRoom::create($data);
            set_alert('success', translate('information_has_been_saved_successfully'));
            if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success']);
            return redirect()->route('hostels.rooms');
        }
        $list=DB::table('hostel_room as t')->leftJoin('hostel as h','h.id','=','t.hostel_id')->leftJoin('hostel_category as c','c.id','=','t.category_id')->leftJoin('branch as b','b.id','=','t.branch_id')->select('t.*','h.name as hostel_name','c.name as category_name','b.name as branch_name')->when(!is_superadmin_loggedin(), fn($q)=>$q->where('t.branch_id',get_loggedin_branch_id()))->orderBy('t.id')->get();
        $branches=is_superadmin_loggedin()? Branch::orderBy('name')->get():collect();
        $hostels=Hostel::when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id',get_loggedin_branch_id()))->get();
        $categories=HostelCategory::when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id',get_loggedin_branch_id()))->get();
        return view('hostels.rooms', ['title'=>translate('hostel_room'),'roomlist'=>$list,'branches'=>$branches,'hostels'=>$hostels,'categories'=>$categories]);
    }

    public function roomEdit(Request $request,$id)
    {
        abort_unless(get_permission('hostel_room','is_edit'),403);
        $room=HostelRoom::findOrFail($id);
        if($request->isMethod('post')){
            $request->validate(['name'=>['required', function($a,$v,$fail) use($id){ $branchId=get_loggedin_branch_id(); $q=HostelRoom::where('name',$v)->where('branch_id',$branchId)->where('id','!=',$id); if($q->exists()) $fail(translate('already_taken')); }],'hostel_id'=>['required'],'category_id'=>['required'],'number_of_beds'=>['required','numeric'],'bed_fee'=>['required','numeric']]);
            $room->update(['name'=>$request->input('name'),'hostel_id'=>$request->input('hostel_id'),'category_id'=>$request->input('category_id'),'no_beds'=>$request->input('number_of_beds'),'bed_fee'=>$request->input('bed_fee'),'remarks'=>$request->input('remarks','')]);
            set_alert('success', translate('information_has_been_updated_successfully'));
            if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success','url'=>route('hostels.rooms')]);
            return redirect()->route('hostels.rooms');
        }
        $hostels=Hostel::when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id',get_loggedin_branch_id()))->get();
        $categories=HostelCategory::when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id',get_loggedin_branch_id()))->get();
        return view('hostels.room_edit', ['title'=>translate('hostels_room_edit'),'room'=>$room,'hostels'=>$hostels,'categories'=>$categories]);
    }

    public function roomDelete(Request $request,$id){
        abort_unless(get_permission('hostel_room','is_delete') || get_permission('room','is_delete'),403);
        $q=HostelRoom::where('id',$id);
        if(!is_superadmin_loggedin()) $q->where('branch_id',get_loggedin_branch_id());
        $q->delete();
        if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success']);
        return back();
    }

    public function assign(Request $request)
    {
        abort_unless(get_permission('room_assign','is_view') || get_permission('hostel_allocation','is_view'),403);
        $branchId=$this->branchId($request);
        $allocationlist=null;
        if($request->isMethod('post')){
            $request->validate(['class_id'=>['required'],'section_id'=>['required']]);
            $allocationlist = $this->allocationReport($request->input('class_id'), $request->input('section_id'), $branchId);
        }
        return view('hostels.assign', ['title'=>translate('allocation_list'),'allocationlist'=>$allocationlist,'branch_id'=>$branchId]);
    }

    private function allocationReport($classID,$sectionID,$branchID){
        $sql="SELECT s.first_name, s.last_name, s.register_no, e.branch_id, e.id as enroll_id, h.name as hostel_name, r.name as room_name, r.bed_fee, rc.name as room_category FROM student as s INNER JOIN enroll as e ON e.student_id = s.id INNER JOIN hostel as h ON h.id = s.hostel_id INNER JOIN hostel_room as r ON r.id = s.room_id LEFT JOIN hostel_category as rc ON rc.id = r.category_id WHERE s.hostel_id != 0 AND s.room_id != 0 AND e.branch_id = ".DB::getPdo()->quote($branchID)." AND e.class_id = ".DB::getPdo()->quote($classID)." AND e.section_id = ".DB::getPdo()->quote($sectionID);
        return DB::select($sql);
    }

    public function getRoomByHostel(Request $request){
        $hostelID=$request->input('hostel_id');
        if(empty($hostelID)) return response('<option value="">'.translate('select_hostel_first').'</option>');
        $rooms=DB::table('hostel_room')->where('hostel_id',$hostelID)->select('id','name','category_id')->get();
        if($rooms->count()){
            $html='<option value="">'.translate('select').'</option>';
            foreach($rooms as $row){ $cat=get_type_name_by_id('hostel_category',$row->category_id); $html.='<option value="'.$row->id.'">'.$row->name.' ('.$cat.')</option>'; }
            return response($html);
        }
        return response('<option value="">'.translate('no_information_available').'</option>');
    }
}
