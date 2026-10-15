<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\PermissionModule;
use App\Models\Role;
use App\Models\StaffPrivilege;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function __construct()
    {
        // legacy checks superadmin only
    }

    private function ensureSuperAdmin(): void
    {
        abort_unless(is_superadmin_loggedin(), 403, translate('access_denied'));
    }

    public function index(Request $request)
    {
        $this->ensureSuperAdmin();
        if ($request->isMethod('post')) {
            $request->validate(['role'=>['required','string','max:50', Rule::unique('roles','name')]]);
            $data = $request->input('role');
            Role::create(['name'=>$data,'prefix'=>strtolower(str_replace(' ','',$data)),'is_system'=>'0']);
            set_alert('success', translate('information_has_been_saved_successfully'));
            return redirect()->route('role.index');
        }
        $roles = Role::orderBy('id')->get();
        return view('role.index', ['title'=>translate('roles'),'roles'=>$roles]);
    }

    public function edit(Request $request, int $id)
    {
        $this->ensureSuperAdmin();
        $role = Role::findOrFail($id);
        if ($request->isMethod('post')) {
            $request->validate(['role'=>['required','string','max:50', Rule::unique('roles','name')->ignore($id)]]);
            $data = $request->input('role');
            $role->update(['name'=>$data,'prefix'=>strtolower(str_replace(' ','',$data))]);
            set_alert('success', translate('information_has_been_updated_successfully'));
            return redirect()->route('role.index');
        }
        return view('role.edit', ['title'=>translate('roles'),'role'=>$role]);
    }

    public function destroy(int $id)
    {
        $this->ensureSuperAdmin();
        $systemRole = [1,2,3,4,5,6,7];
        if (!in_array($id, $systemRole, true)) {
            Role::where('id',$id)->delete();
            StaffPrivilege::where('role_id',$id)->delete();
        }
        if (request()->expectsJson() || request()->ajax()) return response()->json(['status'=>'success']);
        set_alert('success', translate('information_has_been_deleted_successfully'));
        return redirect()->route('role.index');
    }

    public function permission(Request $request, int $role_id)
    {
        $this->ensureSuperAdmin();
        $roleList = Role::whereNotIn('id',[1,6,7])->pluck('id')->toArray();
        // also allow system roles 2-5 for permission editing (legacy used getRoleList which excluded 1,6,7) but we allow all except superadmin check
        $allow = Role::pluck('id')->toArray();
        abort_unless(in_array($role_id, $allow), 403, translate('access_denied'));

        if ($request->isMethod('post')) {
            $privileges = $request->input('privileges', []);
            foreach ($privileges as $permissionId => $value) {
                $data = [
                    'role_id'=>$role_id,
                    'permission_id'=>$permissionId,
                    'is_add'=> isset($value['add']) ? 1 : 0,
                    'is_edit'=> isset($value['edit']) ? 1 : 0,
                    'is_view'=> isset($value['view']) ? 1 : 0,
                    'is_delete'=> isset($value['delete']) ? 1 : 0,
                ];
                $exists = StaffPrivilege::where(['role_id'=>$role_id,'permission_id'=>$permissionId])->first();
                if ($exists) $exists->update($data); else StaffPrivilege::create($data);
            }
            Cache::forget('staff_permissions_'.$role_id);
            set_alert('success', translate('information_has_been_updated_successfully'));
            return redirect()->route('role.permission', $role_id);
        }
        $modules = PermissionModule::orderBy('sorted','asc')->get();
        // preload permissions per module
        $modules->each(function($mod) use ($role_id){
            $mod->permissions = \App\Models\Permission::where('module_id',$mod->id)->orderBy('id')->get()->map(function($perm) use ($role_id){
                $sp = StaffPrivilege::where(['role_id'=>$role_id,'permission_id'=>$perm->id])->first();
                $perm->is_add = $sp->is_add ?? 0;
                $perm->is_edit = $sp->is_edit ?? 0;
                $perm->is_view = $sp->is_view ?? 0;
                $perm->is_delete = $sp->is_delete ?? 0;
                return $perm;
            });
        });
        return view('role.permission', ['title'=>translate('roles'),'role_id'=>$role_id,'modules'=>$modules]);
    }
}
