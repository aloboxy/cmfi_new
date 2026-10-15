<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\Student;
use App\Models\ParentModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $userID = get_loggedin_user_id();
        $roleID = loggedin_role_id();
        if ($roleID == 6) {
            if ($request->isMethod('post')) {
                $validated = $request->validate([
                    'name'=>'required|string|max:255',
                    'relation'=>'required|string|max:255',
                    'occupation'=>'required|string|max:255',
                    'income'=>'nullable|numeric',
                    'mobileno'=>'required|string|max:100',
                    'email'=>['required','email','max:100', Rule::unique('parent','email')->ignore($userID), Rule::unique('login_credential','username')->ignore(get_loggedin_id())],
                    'facebook'=>'nullable|url',
                    'twitter'=>'nullable|url',
                    'linkedin'=>'nullable|url',
                ]);
                $data = $validated;
                if ($request->hasFile('user_photo')) {
                    $dir = public_path('uploads/images/parent');
                    if (!is_dir($dir)) mkdir($dir,0755,true);
                    $name = uniqid().'.'.$request->file('user_photo')->extension();
                    $request->file('user_photo')->move($dir,$name);
                    $data['photo']=$name;
                }
                ParentModel::where('id',$userID)->update([
                    'name'=>$data['name'],'relation'=>$data['relation'],'occupation'=>$data['occupation'],'income'=>$data['income'] ?? null,
                    'mobileno'=>$data['mobileno'],'email'=>$data['email'],
                    'facebook_url'=>$data['facebook'] ?? null,'twitter_url'=>$data['twitter'] ?? null,'linkedin_url'=>$data['linkedin'] ?? null,
                    'photo'=> $data['photo'] ?? ParentModel::where('id',$userID)->value('photo'),
                ]);
                if (isset($data['email'])) DB::table('login_credential')->where('id',get_loggedin_id())->update(['username'=>$data['email']]);
                set_alert('success', translate('information_has_been_updated_successfully'));
                return redirect()->route('profile.index');
            }
            $parent = ParentModel::find($userID);
            return view('profile.parent', ['title'=>translate('profile').' '.translate('edit'),'parent'=>$parent]);
        } elseif ($roleID == 7) {
            if ($request->isMethod('post')) {
                $validated = $request->validate([
                    'first_name'=>'required|string|max:255',
                    'last_name'=>'required|string|max:255',
                    'mobileno'=>'required|string|max:100',
                    'email'=>['required','email', Rule::unique('student','email')->ignore($userID), Rule::unique('login_credential','username')->ignore(get_loggedin_id())],
                ]);
                if ($request->hasFile('user_photo')) {
                    $dir = public_path('uploads/images/student');
                    if (!is_dir($dir)) mkdir($dir,0755,true);
                    $name = uniqid().'.'.$request->file('user_photo')->extension();
                    $request->file('user_photo')->move($dir,$name);
                    $validated['photo']=$name;
                }
                $upd = ['first_name'=>$validated['first_name'],'last_name'=>$validated['last_name'],'mobileno'=>$validated['mobileno'],'email'=>$validated['email']];
                if (isset($validated['photo'])) $upd['photo']=$validated['photo'];
                Student::where('id',$userID)->update($upd);
                DB::table('login_credential')->where('id',get_loggedin_id())->update(['username'=>$validated['email']]);
                set_alert('success', translate('information_has_been_updated_successfully'));
                return redirect()->route('profile.index');
            }
            $student = Student::find($userID);
            return view('profile.student', ['title'=>translate('profile').' '.translate('edit'),'student'=>$student]);
        } else {
            if ($request->isMethod('post')) {
                $rules = [
                    'name'=>'required|string|max:255',
                    'mobile_no'=>'required|string|max:100',
                    'present_address'=>'required|string',
                    'email'=>['required','email', Rule::unique('staff','email')->ignore($userID), Rule::unique('login_credential','username')->ignore(get_loggedin_id())],
                    'facebook'=>'nullable|url',
                    'twitter'=>'nullable|url',
                    'linkedin'=>'nullable|url',
                ];
                if (is_admin_loggedin()) {
                    $rules['designation_id']='required|integer';
                    $rules['department_id']='required|integer';
                    $rules['joining_date']='required|string';
                    $rules['qualification']='required|string';
                }
                $validated = $request->validate($rules);
                $upd = [
                    'name'=>$validated['name'],'mobileno'=>$validated['mobile_no'],'present_address'=>$validated['present_address'],
                    'email'=>$validated['email'],
                    'facebook_url'=>$validated['facebook'] ?? null,'twitter_url'=>$validated['twitter'] ?? null,'linkedin_url'=>$validated['linkedin'] ?? null,
                ];
                if (is_admin_loggedin()) {
                    $upd['designation']=$validated['designation_id'];
                    $upd['department']=$validated['department_id'];
                    $upd['joining_date']=$validated['joining_date'];
                    $upd['qualification']=$validated['qualification'];
                }
                if ($request->hasFile('user_photo')) {
                    $dir = public_path('uploads/images/staff');
                    if (!is_dir($dir)) mkdir($dir,0755,true);
                    $name = uniqid().'.'.$request->file('user_photo')->extension();
                    $request->file('user_photo')->move($dir,$name);
                    $upd['photo']=$name;
                }
                Staff::where('id',$userID)->update($upd);
                DB::table('login_credential')->where('id',get_loggedin_id())->update(['username'=>$validated['email']]);
                set_alert('success', translate('information_has_been_updated_successfully'));
                return redirect()->route('profile.index');
            }
            $staff = Staff::find($userID);
            return view('profile.employee', ['title'=>translate('profile').' '.translate('edit'),'staff'=>$staff]);
        }
    }

    public function password(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'current_password'=>['required','min:4'],
                'new_password'=>['required','min:4'],
                'confirm_password'=>['required','same:new_password'],
            ]);
            $user = DB::table('login_credential')->where('id', get_loggedin_id())->first();
            $hashed = $user->password ?? '';
            $valid = Hash::check($request->input('current_password'), $hashed) || (function_exists('password_verify') && password_verify($request->input('current_password'), $hashed)) || $request->input('current_password') === $hashed;
            // try legacy verify via app_lib if available
            if (!$valid) {
                // use Laravel hash check only; fallback
                return back()->withErrors(['current_password'=>translate('current_password_is_invalid')]);
            }
            DB::table('login_credential')->where('id', get_loggedin_id())->update(['password'=> Hash::make($request->input('new_password'))]);
            set_alert('success', translate('password_has_been_changed'));
            return redirect()->route('profile.password');
        }
        return view('profile.password_change', ['title'=>translate('profile')]);
    }
}
