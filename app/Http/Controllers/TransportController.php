<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\TransportAssign;
use App\Models\TransportRoute;
use App\Models\TransportStoppage;
use App\Models\TransportVehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransportController extends Controller
{
    private function branchId(Request $r): int { return is_superadmin_loggedin()? (int)($r->input('branch_id')?:get_loggedin_branch_id()): (int)get_loggedin_branch_id(); }

    // Routes
    public function routes(Request $request)
    {
        abort_unless(get_permission('routes','is_view') || get_permission('transport_route','is_view'),403);
        if($request->isMethod('post')){
            abort_unless(get_permission('transport_route','is_add') || get_permission('routes','is_add'),403);
            if(is_superadmin_loggedin()) $request->validate(['branch_id'=>['required']]);
            $request->validate(['route_name'=>['required'],'start_place'=>['required'],'stop_place'=>['required']]);
            TransportRoute::create(['name'=>$request->input('route_name'),'start_place'=>$request->input('start_place'),'stop_place'=>$request->input('stop_place'),'remarks'=>$request->input('remarks',''),'branch_id'=>$this->branchId($request)]);
            set_alert('success', translate('information_has_been_saved_successfully'));
            if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success','url'=>route('transport.routes')]);
            return redirect()->route('transport.routes');
        }
        $list=DB::table('transport_route as t')->leftJoin('branch as b','b.id','=','t.branch_id')->select('t.*','b.name as branch_name')->when(!is_superadmin_loggedin(), fn($q)=>$q->where('t.branch_id',get_loggedin_branch_id()))->orderBy('t.id')->get();
        $branches=is_superadmin_loggedin()? Branch::orderBy('name')->get():collect();
        return view('transport.routes', ['title'=>translate('route_master'),'transportlist'=>$list,'branches'=>$branches]);
    }

    public function routeEdit(Request $request,$id)
    {
        abort_unless(get_permission('transport_route','is_edit'),403);
        $route=TransportRoute::findOrFail($id);
        if($request->isMethod('post')){
            $request->validate(['route_name'=>['required'],'start_place'=>['required'],'stop_place'=>['required']]);
            $route->update(['name'=>$request->input('route_name'),'start_place'=>$request->input('start_place'),'stop_place'=>$request->input('stop_place'),'remarks'=>$request->input('remarks',''),'branch_id'=>$request->input('branch_id') ?? $route->branch_id]);
            set_alert('success', translate('information_has_been_updated_successfully'));
            if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success','url'=>route('transport.routes')]);
            return redirect()->route('transport.routes');
        }
        return view('transport.route_edit', ['title'=>translate('route_master'),'route'=>$route]);
    }

    public function routeDelete(Request $request,$id){
        abort_unless(get_permission('transport_route','is_delete'),403);
        $q=TransportRoute::where('id',$id);
        if(!is_superadmin_loggedin()) $q->where('branch_id',get_loggedin_branch_id());
        $q->delete();
        if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success']);
        return back();
    }

    // Vehicles
    public function vehicles(Request $request)
    {
        abort_unless(get_permission('vehicles','is_view') || get_permission('transport_vehicle','is_view'),403);
        if($request->isMethod('post')){
            abort_unless(get_permission('transport_vehicle','is_add'),403);
            if(is_superadmin_loggedin()) $request->validate(['branch_id'=>['required']]);
            $request->validate(['vehicle_no'=>['required'],'capacity'=>['required','numeric'],'driver_name'=>['required'],'driver_phone'=>['required'],'driver_license'=>['required']]);
            TransportVehicle::create(['vehicle_no'=>$request->input('vehicle_no'),'capacity'=>$request->input('capacity'),'insurance_renewal'=>$request->input('insurance_renewal',''),'driver_name'=>$request->input('driver_name'),'driver_phone'=>$request->input('driver_phone'),'driver_license'=>$request->input('driver_license'),'branch_id'=>$this->branchId($request)]);
            set_alert('success', translate('information_has_been_saved_successfully'));
            if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success','url'=>route('transport.vehicles')]);
            return redirect()->route('transport.vehicles');
        }
        $list=DB::table('transport_vehicle as t')->leftJoin('branch as b','b.id','=','t.branch_id')->select('t.*','b.name as branch_name')->when(!is_superadmin_loggedin(), fn($q)=>$q->where('t.branch_id',get_loggedin_branch_id()))->orderBy('t.id')->get();
        $branches=is_superadmin_loggedin()? Branch::orderBy('name')->get():collect();
        return view('transport.vehicles', ['title'=>translate('vehicle_master'),'transportlist'=>$list,'branches'=>$branches]);
    }

    public function vehicleEdit(Request $request,$id)
    {
        abort_unless(get_permission('transport_vehicle','is_edit'),403);
        $vehicle=TransportVehicle::findOrFail($id);
        if($request->isMethod('post')){
            $request->validate(['vehicle_no'=>['required'],'capacity'=>['required','numeric'],'driver_name'=>['required'],'driver_phone'=>['required'],'driver_license'=>['required']]);
            $vehicle->update(['vehicle_no'=>$request->input('vehicle_no'),'capacity'=>$request->input('capacity'),'insurance_renewal'=>$request->input('insurance_renewal',''),'driver_name'=>$request->input('driver_name'),'driver_phone'=>$request->input('driver_phone'),'driver_license'=>$request->input('driver_license')]);
            set_alert('success', translate('information_has_been_updated_successfully'));
            if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success','url'=>route('transport.vehicles')]);
            return redirect()->route('transport.vehicles');
        }
        return view('transport.vehicle_edit', ['title'=>translate('vehicle_master'),'vehicle'=>$vehicle]);
    }

    public function vehicleDelete(Request $request,$id){
        // legacy checks transport_route permission for delete
        abort_unless(get_permission('transport_route','is_delete') || get_permission('transport_vehicle','is_delete') || get_permission('vehicles','is_delete'),403);
        $q=TransportVehicle::where('id',$id);
        if(!is_superadmin_loggedin()) $q->where('branch_id',get_loggedin_branch_id());
        $q->delete();
        if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success']);
        return back();
    }

    // Assign
    public function assign(Request $request)
    {
        abort_unless(get_permission('assign_vehicle','is_view') || get_permission('transport_assign','is_view'),403);
        $branchId=$this->branchId($request);
        if($request->isMethod('post')){
            abort_unless(get_permission('transport_assign','is_add'),403);
            if(is_superadmin_loggedin()) $request->validate(['branch_id'=>['required']]);
            $request->validate([
                'route_id'=>['required', function($a,$v,$fail){ $q=TransportAssign::where('route_id',$v); if($q->exists()) $fail('This route is already assigned.'); }],
                'stoppage_id'=>['required'],'vehicle'=>['required','array'],
            ]);
            $vehicles=$request->input('vehicle');
            $rows=[];
            foreach($vehicles as $vehicle){ $rows[]=['branch_id'=>$branchId,'route_id'=>$request->input('route_id'),'stoppage_id'=>$request->input('stoppage_id'),'vehicle_id'=>$vehicle]; }
            DB::table('transport_assign')->insert($rows);
            set_alert('success', translate('information_has_been_saved_successfully'));
            if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success','url'=>route('transport.assign')]);
            return redirect()->route('transport.assign');
        }
        // assign list grouped
        $assignList = DB::table('transport_assign as ta')->leftJoin('transport_route as r','r.id','=','ta.route_id')->leftJoin('transport_stoppage as sp','sp.id','=','ta.stoppage_id')->select('ta.route_id','ta.stoppage_id','ta.branch_id','r.name','r.start_place','r.stop_place','sp.stop_position','sp.stop_time','sp.route_fare')->groupBy('ta.route_id','ta.stoppage_id','ta.branch_id')->when(!is_superadmin_loggedin(), fn($q)=>$q->where('ta.branch_id',get_loggedin_branch_id()))->get();
        // for display vehicle list
        foreach($assignList as $a){ $a->vehicles = DB::table('transport_assign as ta')->leftJoin('transport_vehicle as v','v.id','=','ta.vehicle_id')->where('ta.route_id',$a->route_id)->select('v.vehicle_no')->get(); }
        $routes = TransportRoute::when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id',get_loggedin_branch_id()))->get();
        $stoppages = TransportStoppage::when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id',get_loggedin_branch_id()))->get();
        $vehicles = TransportVehicle::when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id',get_loggedin_branch_id()))->get();
        $branches=is_superadmin_loggedin()? Branch::orderBy('name')->get():collect();
        return view('transport.assign', ['title'=>translate('assign_vehicle'),'assignList'=>$assignList,'routes'=>$routes,'stoppages'=>$stoppages,'vehicles'=>$vehicles,'branches'=>$branches,'branch_id'=>$branchId]);
    }

    public function assignEdit(Request $request,$id)
    {
        abort_unless(get_permission('transport_assign','is_edit'),403);
        $assign = DB::table('transport_assign')->where('route_id',$id)->when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id',get_loggedin_branch_id()))->first();
        abort_if(!$assign,404);
        if($request->isMethod('post')){
            $request->validate(['route_id'=>['required'],'stoppage_id'=>['required'],'vehicle'=>['required','array']]);
            $branchId=$this->branchId($request);
            $routeID=$request->input('route_id');
            $stoppageID=$request->input('stoppage_id');
            $vehicles=$request->input('vehicle');
            foreach($vehicles as $vehicle){
                $data=['branch_id'=>$branchId,'route_id'=>$id,'vehicle_id'=>$vehicle];
                $q=DB::table('transport_assign')->where($data)->first();
                if(!$q) DB::table('transport_assign')->insert(['branch_id'=>$branchId,'route_id'=>$routeID,'stoppage_id'=>$stoppageID,'vehicle_id'=>$vehicle]);
                else DB::table('transport_assign')->where('id',$q->id)->update(['stoppage_id'=>$stoppageID,'route_id'=>$routeID]);
            }
            DB::table('transport_assign')->where('route_id',$routeID)->where('branch_id',$branchId)->whereNotIn('vehicle_id',$vehicles)->delete();
            set_alert('success', translate('information_has_been_updated_successfully'));
            if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success','url'=>route('transport.assign')]);
            return redirect()->route('transport.assign');
        }
        $routes = TransportRoute::when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id',get_loggedin_branch_id()))->get();
        $stoppages = TransportStoppage::when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id',get_loggedin_branch_id()))->get();
        $vehiclesAll = TransportVehicle::when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id',get_loggedin_branch_id()))->get();
        $selectedVehicles = DB::table('transport_assign')->where('route_id',$id)->pluck('vehicle_id')->toArray();
        return view('transport.assign_edit', ['title'=>translate('assign_vehicle'),'assign'=>$assign,'routes'=>$routes,'stoppages'=>$stoppages,'vehicles'=>$vehiclesAll,'selectedVehicles'=>$selectedVehicles]);
    }

    public function assignDelete(Request $request,$id){
        abort_unless(get_permission('transport_assign','is_delete'),403);
        $q=DB::table('transport_assign')->where('route_id',$id);
        if(!is_superadmin_loggedin()) $q->where('branch_id',get_loggedin_branch_id());
        $q->delete();
        if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success']);
        return back();
    }

    public function getVehicleByBranch(Request $request){
        $branchId=$this->branchId($request);
        if(empty($branchId)) return response('<option value="">'.translate('first_select_the_branch').'</option>');
        $result=TransportVehicle::where('branch_id',$branchId)->get(['id','vehicle_no']);
        if($result->count()){
            $html="<option value=''>".translate('select')."</option>";
            foreach($result as $row) $html.='<option value="'.$row->id.'">'.$row->vehicle_no.'</option>';
            return response($html);
        }
        return response('<option value="">'.translate('no_selection_available').'</option>');
    }

    public function getStoppageByBranch(Request $request){
        $branchId=$this->branchId($request);
        if(empty($branchId)) return response('<option value="">'.translate('first_select_the_branch').'</option>');
        $result=TransportStoppage::where('branch_id',$branchId)->get(['id','stop_position']);
        if($result->count()){
            $html="<option value=''>".translate('select')."</option>";
            foreach($result as $row) $html.='<option value="'.$row->id.'">'.$row->stop_position.'</option>';
            return response($html);
        }
        return response('<option value="">'.translate('no_selection_available').'</option>');
    }
}
