<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\ParentModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ParentsController extends Controller
{
    private function resolveBranchId(Request $request): int
    {
        if (is_superadmin_loggedin()) return (int) $request->input('branch_id', 0);
        return (int) get_loggedin_branch_id();
    }

    private function handlePhotoUpload(Request $request, $old=null)
    {
        if ($request->hasFile('user_photo')) {
            $file = $request->file('user_photo');
            $ext = strtolower($file->getClientOriginalExtension());
            if (!in_array($ext, ['jpg','jpeg','png'])) return $old ?? 'defualt.png';
            if ($file->getSize()>2097152) return $old ?? 'defualt.png';
            $dir = public_path('uploads/images/parent');
            if (!is_dir($dir)) mkdir($dir,0755,true);
            $filename = uniqid().'.'.$ext;
            $file->move($dir,$filename);
            if (!empty($old) && $old!=='defualt.png' && file_exists($dir.'/'.$old)) @unlink($dir.'/'.$old);
            return $filename;
        }
        return $old ?? 'defualt.png';
    }

    public function index()
    {
        abort_unless(get_permission('parent','is_view') || get_permission('parents','is_view'), 403, translate('access_denied'));
        $branchId = is_superadmin_loggedin() ? null : get_loggedin_branch_id();
        $parents = $this->getParentList($branchId, 1);
        return view('parents.index', [
            'title'=>translate('parents_list'),
            'parents'=>$parents,
            'branch_id'=>$branchId,
        ]);
    }

    public function create(Request $request)
    {
        abort_unless(get_permission('parent','is_add') || get_permission('parents','is_add'), 403, translate('access_denied'));
        if ($request->isMethod('post')) {
            $rules = [
                'name'=>['required'],
                'relation'=>['required'],
                'occupation'=>['required'],
                'income'=>['nullable','numeric'],
                'mobileno'=>['required'],
                'email'=>['required','email', Rule::unique('login_credential','username')],
                'password'=>['required','min:4'],
                'retype_password'=>['required','same:password'],
                'user_photo'=>['nullable','image','mimes:jpg,jpeg,png','max:2048'],
                'facebook'=>['nullable','url'],
                'twitter'=>['nullable','url'],
                'linkedin'=>['nullable','url'],
            ];
            if (is_superadmin_loggedin()) $rules['branch_id']=['required'];
            $request->validate($rules);
            $branchID = $this->resolveBranchId($request);
            $photo = $this->handlePhotoUpload($request, null);
            $parent = ParentModel::create([
                'branch_id'=>$branchID,
                'name'=>$request->input('name'),
                'relation'=>$request->input('relation'),
                'father_name'=>$request->input('father_name'),
                'mother_name'=>$request->input('mother_name'),
                'occupation'=>$request->input('occupation'),
                'income'=>$request->input('income'),
                'education'=>$request->input('education'),
                'email'=>$request->input('email'),
                'mobileno'=>$request->input('mobileno'),
                'address'=>$request->input('address'),
                'city'=>$request->input('city'),
                'state'=>$request->input('state'),
                'photo'=>$photo,
                'facebook_url'=>$request->input('facebook'),
                'linkedin_url'=>$request->input('linkedin'),
                'twitter_url'=>$request->input('twitter'),
            ]);
            User::create([
                'username'=>$request->input('email'),
                'role'=>6,
                'user_id'=>$parent->id,
                'password'=>Hash::make($request->input('password')),
                'active'=>1,
            ]);
            set_alert('success', translate('information_has_been_saved_successfully'));
            return redirect()->route('parents.create');
        }
        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();
        $branchId = is_superadmin_loggedin() ? null : get_loggedin_branch_id();
        return view('parents.create', [
            'title'=>translate('add_parent'),
            'branches'=>$branches,
            'branch_id'=>$branchId,
        ]);
    }

    public function profile($id, Request $request)
    {
        abort_unless(get_permission('parent','is_edit') || get_permission('parents','is_edit'), 403, translate('access_denied'));
        $parent = $this->getSingleParent($id);
        if ($request->isMethod('post')) {
            $rules = [
                'name'=>['required'],
                'relation'=>['required'],
                'occupation'=>['required'],
                'income'=>['nullable','numeric'],
                'mobileno'=>['required'],
                'email'=>['required','email', Rule::unique('login_credential','username')->where(fn($q)=> $q->where('role',6)->where('user_id','!=',$id))],
                'user_photo'=>['nullable','image','mimes:jpg,jpeg,png','max:2048'],
            ];
            if (is_superadmin_loggedin()) $rules['branch_id']=['required'];
            $request->validate($rules);
            $photo = $this->handlePhotoUpload($request, $parent['photo']);
            ParentModel::where('id',$id)->update([
                'branch_id'=> $this->resolveBranchId($request) ?: $parent['branch_id'],
                'name'=>$request->input('name'),
                'relation'=>$request->input('relation'),
                'father_name'=>$request->input('father_name'),
                'mother_name'=>$request->input('mother_name'),
                'occupation'=>$request->input('occupation'),
                'income'=>$request->input('income'),
                'education'=>$request->input('education'),
                'email'=>$request->input('email'),
                'mobileno'=>$request->input('mobileno'),
                'address'=>$request->input('address'),
                'city'=>$request->input('city'),
                'state'=>$request->input('state'),
                'photo'=>$photo,
                'facebook_url'=>$request->input('facebook'),
                'linkedin_url'=>$request->input('linkedin'),
                'twitter_url'=>$request->input('twitter'),
            ]);
            DB::table('login_credential')->where(['role'=>6,'user_id'=>$id])->update(['username'=>$request->input('email')]);
            set_alert('success', translate('information_has_been_saved_successfully'));
            return redirect()->route('parents.profile', $id);
        }
        // fetch children for display
        $children = DB::table('enroll as e')->join('student as s','s.id','=','e.student_id')->join('login_credential as l', function($j){$j->on('l.user_id','=','s.id')->where('l.role','=',7);})->leftJoin('class as c','c.id','=','e.class_id')->leftJoin('section as se','se.id','=','e.section_id')->where('s.parent_id',$id)->where('l.active',1)->select('s.id','s.photo', DB::raw('CONCAT(s.first_name," ",s.last_name) as fullname'),'c.name as class_name','se.name as section_name')->get();
        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();
        return view('parents.profile', [
            'title'=>translate('parents_profile'),
            'parent'=>$parent,
            'branches'=>$branches,
            'children'=>$children,
        ]);
    }

    private function getSingleParent($id)
    {
        $q = DB::table('parent')->join('login_credential','login_credential.user_id','=','parent.id')->where('login_credential.role',6)->where('parent.id',$id);
        if (!is_superadmin_loggedin()) $q->where('parent.branch_id', get_loggedin_branch_id());
        $row = $q->select('parent.*','login_credential.role as role_id','login_credential.active','login_credential.username','login_credential.id as login_id')->first();
        abort_if(!$row, 404);
        return (array)$row;
    }

    private function getParentList($branchID=null,$active=1)
    {
        $q = DB::table('parent')->join('login_credential','login_credential.user_id','=','parent.id')->where('login_credential.role',6)->where('login_credential.active',$active);
        if (!empty($branchID)) $q->where('parent.branch_id',$branchID);
        elseif (!is_superadmin_loggedin()) $q->where('parent.branch_id', get_loggedin_branch_id());
        return $q->select('parent.*','login_credential.active as active_status')->orderBy('parent.id','asc')->get();
    }

    public function destroy($id, Request $request)
    {
        abort_unless(get_permission('parent','is_delete') || get_permission('parents','is_delete'), 403, translate('access_denied'));
        $q = ParentModel::where('id',$id);
        if (!is_superadmin_loggedin()) $q->where('branch_id', get_loggedin_branch_id());
        $deleted = $q->delete();
        if ($deleted) DB::table('login_credential')->where(['user_id'=>$id,'role'=>6])->delete();
        if ($request->expectsJson()) return response()->json(['status'=>'success']);
        set_alert('success', translate('information_has_been_deleted_successfully'));
        return back();
    }

    public function disableAuthentication(Request $request)
    {
        abort_unless(get_permission('disable_authentication','is_view') || get_permission('parent_disable_authentication','is_view'), 403, translate('access_denied'));
        if ($request->isMethod('post') && $request->input('auth')) {
            abort_unless(get_permission('disable_authentication','is_add') || get_permission('parent_disable_authentication','is_add'), 403, translate('access_denied'));
            $list = $request->input('views_bulk_operations');
            if (!empty($list)) {
                foreach ($list as $id) DB::table('login_credential')->where(['role'=>6,'user_id'=>$id])->update(['active'=>1]);
                set_alert('success', translate('information_has_been_updated_successfully'));
            } else {
                set_alert('error','Please select at least one item');
            }
            return redirect()->route('parents.disable_authentication');
        }
        $branchID = is_superadmin_loggedin() ? ($request->input('branch_id') ?? null) : get_loggedin_branch_id();
        $parentslist = $this->getParentList($branchID, 0);
        return view('parents.disable_authentication', [
            'title'=>translate('deactivate_account'),
            'parentslist'=>$parentslist,
        ]);
    }

    public function changePassword(Request $request)
    {
        abort_unless(get_permission('parent','is_edit') || get_permission('parents','is_edit'), 403, translate('access_denied'));
        if (!$request->input('authentication')) $request->validate(['password'=>['required','min:4']]);
        $parentID = $request->input('parent_id');
        if (!$request->input('authentication')) {
            DB::table('login_credential')->where('role',6)->where('user_id',$parentID)->update(['password'=>Hash::make($request->input('password'))]);
        } else {
            DB::table('login_credential')->where('role',6)->where('user_id',$parentID)->update(['active'=>0]);
        }
        set_alert('success', translate('information_has_been_updated_successfully'));
        return response()->json(['status'=>'success']);
    }

    public function selectChild($id)
    {
        if (is_parent_loggedin()) {
            $exists = DB::table('student')->where(['id'=>$id,'parent_id'=>get_loggedin_user_id()])->exists();
            if ($exists) session(['myChildren_id'=>$id]);
            return redirect()->back();
        }
        return redirect('/');
    }

    public function myChildren()
    {
        if (is_parent_loggedin()) {
            session()->forget('myChildren_id');
            return redirect()->route('dashboard');
        }
        return redirect('/');
    }
}
