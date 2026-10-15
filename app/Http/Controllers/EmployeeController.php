<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Staff;
use App\Models\StaffBankAccount;
use App\Models\StaffDepartment;
use App\Models\StaffDesignation;
use App\Models\StaffDocument;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    private function resolveBranchId(Request $request): int
    {
        if (is_superadmin_loggedin()) return (int) $request->input('branch_id', 0);
        return (int) get_loggedin_branch_id();
    }

    private function handlePhotoUpload(Request $request, $old=null)
    {
        if ($request->hasFile('user_photo')) {
            $file=$request->file('user_photo');
            $ext=strtolower($file->getClientOriginalExtension());
            if (!in_array($ext,['jpg','jpeg','png'])) return $old ?? 'defualt.png';
            if ($file->getSize()>2097152) return $old ?? 'defualt.png';
            $dir=public_path('uploads/images/staff');
            if (!is_dir($dir)) mkdir($dir,0755,true);
            $filename=uniqid().'.'.$ext;
            $file->move($dir,$filename);
            if (!empty($old) && $old!=='defualt.png' && file_exists($dir.'/'.$old)) @unlink($dir.'/'.$old);
            return $filename;
        }
        return $old ?? 'defualt.png';
    }

    public function index(Request $request, $role=2)
    {
        // legacy view takes role param; we also support filter via query
        if ($request->route('role')) $role = $request->route('role');
        if (in_array($role,[1,6,7])) abort(403, translate('access_denied'));
        abort_unless(get_permission('employee','is_view'),403,translate('access_denied'));
        $branchID = is_superadmin_loggedin() ? ($request->input('branch_id') ?? null) : get_loggedin_branch_id();
        // if branchID empty and superadmin without filter, show all branches? We'll show for first branch or all
        $stafflist = $request->input('role') ? $this->getStaffList($branchID, $request->input('role'), 1) : $this->getStaffList($branchID, $role, 1);
        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();
        $roles = DB::table('roles')->whereNotIn('id',[1,6,7])->get();
        return view('employee.index', [
            'title'=>translate('employee'),
            'stafflist'=>$stafflist,
            'branches'=>$branches,
            'roles'=>$roles,
            'act_role'=>$role,
            'branch_id'=>$branchID,
        ]);
    }

    private function getStaffList($branchID,$role_id,$active=1)
    {
        $q = DB::table('staff')->join('login_credential','login_credential.user_id','=','staff.id')->join('roles','roles.id','=','login_credential.role')->leftJoin('staff_designation','staff_designation.id','=','staff.designation')->leftJoin('staff_department','staff_department.id','=','staff.department')->where('login_credential.role',$role_id)->where('login_credential.active',$active)->whereNotIn('login_credential.role',[6,7]);
        if (!empty($branchID)) $q->where('staff.branch_id',$branchID);
        elseif (!is_superadmin_loggedin()) $q->where('staff.branch_id', get_loggedin_branch_id());
        return $q->select('staff.*','staff_designation.name as designation_name','staff_department.name as department_name','login_credential.role as role_id','roles.name as role')->orderBy('staff.id','asc')->get();
    }

    public function create(Request $request)
    {
        abort_unless(get_permission('employee','is_add'),403,translate('access_denied'));
        if ($request->isMethod('post')) {
            $rules = [
                'name'=>['required'],
                'mobile_no'=>['required'],
                'present_address'=>['required'],
                'designation_id'=>['required','integer'],
                'department_id'=>['required','integer'],
                'joining_date'=>['required'],
                'qualification'=>['required'],
                'user_role'=>['required', function($attr,$val,$fail){ if(in_array($val,[1,6,7])) $fail(translate('selected_role_restrictions')); }],
                'email'=>['required','email', Rule::unique('login_credential','username')],
                'password'=>['required','min:4'],
                'retype_password'=>['required','same:password'],
                'user_photo'=>['nullable','image','mimes:jpg,jpeg,png','max:2048'],
            ];
            if (! $request->input('chkskipped')) {
                $rules['bank_name']=['required'];
                $rules['holder_name']=['required'];
                $rules['bank_branch']=['required'];
                $rules['account_no']=['required'];
            }
            if (is_superadmin_loggedin()) $rules['branch_id']=['required'];
            $request->validate($rules);
            $branchID = $this->resolveBranchId($request);
            $photo = $this->handlePhotoUpload($request,null);
            $staff = Staff::create([
                'branch_id'=>$branchID,
                'name'=>$request->input('name'),
                'sex'=>$request->input('sex'),
                'religion'=>$request->input('religion'),
                'blood_group'=>$request->input('blood_group'),
                'birthday'=>$request->input('birthday'),
                'mobileno'=>$request->input('mobile_no'),
                'present_address'=>$request->input('present_address'),
                'permanent_address'=>$request->input('permanent_address'),
                'photo'=>$photo,
                'designation'=>$request->input('designation_id'),
                'department'=>$request->input('department_id'),
                'joining_date'=>date('Y-m-d', strtotime($request->input('joining_date'))),
                'qualification'=>$request->input('qualification'),
                'email'=>$request->input('email'),
                'facebook_url'=>$request->input('facebook'),
                'linkedin_url'=>$request->input('linkedin'),
                'twitter_url'=>$request->input('twitter'),
                'staff_id'=>substr(md5(rand().microtime().time().uniqid()),3,7),
                'hash'=>md5(rand().microtime().time().uniqid()),
            ]);
            User::create([
                'username'=>$request->input('email'),
                'role'=>$request->input('user_role'),
                'user_id'=>$staff->id,
                'password'=>Hash::make($request->input('password')),
                'active'=>1,
            ]);
            if (! $request->input('chkskipped')) {
                StaffBankAccount::create([
                    'staff_id'=>$staff->id,
                    'bank_name'=>$request->input('bank_name'),
                    'holder_name'=>$request->input('holder_name'),
                    'bank_branch'=>$request->input('bank_branch'),
                    'bank_address'=>$request->input('bank_address'),
                    'ifsc_code'=>$request->input('ifsc_code'),
                    'account_no'=>$request->input('account_no'),
                ]);
            }
            set_alert('success', translate('information_has_been_saved_successfully'));
            return redirect()->route('employee.index', $request->input('user_role'));
        }
        $branchID = is_superadmin_loggedin() ? null : get_loggedin_branch_id();
        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();
        $departments = $branchID ? StaffDepartment::where('branch_id',$branchID)->get() : collect();
        $designations = $branchID ? StaffDesignation::where('branch_id',$branchID)->get() : collect();
        $roles = DB::table('roles')->whereNotIn('id',[1,6,7])->get();
        return view('employee.create', [
            'title'=>translate('add_employee'),
            'branches'=>$branches,
            'branch_id'=>$branchID,
            'departments'=>$departments,
            'designations'=>$designations,
            'roles'=>$roles,
        ]);
    }

    public function profile($id, Request $request)
    {
        abort_unless(get_permission('employee','is_edit'),403,translate('access_denied'));
        $staffData = $this->getSingleStaff($id);
        abort_if(!$staffData,404);
        if ($request->isMethod('post') && $request->input('submit')=='update') {
            $rules = [
                'name'=>['required'],
                'mobile_no'=>['required'],
                'present_address'=>['required'],
                'designation_id'=>['required'],
                'department_id'=>['required'],
                'joining_date'=>['required'],
                'qualification'=>['required'],
                'user_role'=>['required', function($a,$v,$f){ if(in_array($v,[1,6,7])) $f(translate('selected_role_restrictions')); }],
                'email'=>['required','email', Rule::unique('login_credential','username')->where(fn($q)=> $q->whereNotIn('role',[6,7])->where('user_id','!=',$id))],
                'user_photo'=>['nullable','image','mimes:jpg,jpeg,png','max:2048'],
            ];
            if (is_superadmin_loggedin()) $rules['branch_id']=['required'];
            $request->validate($rules);
            $branchID = $this->resolveBranchId($request) ?: $staffData['branch_id'];
            $photo = $this->handlePhotoUpload($request, $staffData['photo']);
            Staff::where('id',$id)->update([
                'branch_id'=>$branchID,
                'name'=>$request->input('name'),
                'sex'=>$request->input('sex'),
                'religion'=>$request->input('religion'),
                'blood_group'=>$request->input('blood_group'),
                'birthday'=>$request->input('birthday'),
                'mobileno'=>$request->input('mobile_no'),
                'present_address'=>$request->input('present_address'),
                'permanent_address'=>$request->input('permanent_address'),
                'photo'=>$photo,
                'designation'=>$request->input('designation_id'),
                'department'=>$request->input('department_id'),
                'joining_date'=>date('Y-m-d', strtotime($request->input('joining_date'))),
                'qualification'=>$request->input('qualification'),
                'email'=>$request->input('email'),
                'facebook_url'=>$request->input('facebook'),
                'linkedin_url'=>$request->input('linkedin'),
                'twitter_url'=>$request->input('twitter'),
            ]);
            DB::table('login_credential')->where('user_id',$id)->whereNotIn('role',[6,7])->update(['username'=>$request->input('email'),'role'=>$request->input('user_role')]);
            set_alert('success', translate('information_has_been_updated_successfully'));
            return redirect()->route('employee.profile',$id);
        }
        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();
        $departments = StaffDepartment::where('branch_id',$staffData['branch_id'])->get();
        $designations = StaffDesignation::where('branch_id',$staffData['branch_id'])->get();
        $roles = DB::table('roles')->whereNotIn('id',[1,6,7])->get();
        $bank = StaffBankAccount::where('staff_id',$id)->first();
        $documents = StaffDocument::where('staff_id',$id)->get();
        $categorylist = ['1'=>'Resume File','2'=>'Offer Letter','3'=>'Joining Letter','4'=>'Experience Certificate','5'=>'Resignation Letter','6'=>'Other Documents'];
        return view('employee.profile', [
            'title'=>translate('employee_profile'),
            'staff'=>$staffData,
            'branches'=>$branches,
            'departments'=>$departments,
            'designations'=>$designations,
            'roles'=>$roles,
            'bank'=>$bank,
            'documents'=>$documents,
            'categorylist'=>$categorylist,
        ]);
    }

    private function getSingleStaff($id)
    {
        $q = DB::table('staff')->join('login_credential','login_credential.user_id','=','staff.id')->join('roles','roles.id','=','login_credential.role')->leftJoin('staff_designation','staff_designation.id','=','staff.designation')->leftJoin('staff_department','staff_department.id','=','staff.department')->where('staff.id',$id)->whereNotIn('login_credential.role',[6,7]);
        if (!is_superadmin_loggedin()) $q->where('staff.branch_id', get_loggedin_branch_id());
        $row = $q->select('staff.*','staff_designation.name as designation_name','staff_department.name as department_name','login_credential.role as role_id','login_credential.active','login_credential.username','roles.name as role')->first();
        if (!$row) abort(404);
        return (array)$row;
    }

    public function destroy($id, Request $request)
    {
        abort_unless(get_permission('employee','is_delete'),403,translate('access_denied'));
        $q = Staff::where('id',$id);
        if (!is_superadmin_loggedin()) $q->where('branch_id', get_loggedin_branch_id());
        $deleted = $q->delete();
        if ($deleted) DB::table('login_credential')->where('user_id',$id)->whereNotIn('role',[1,6,7])->delete();
        if ($request->expectsJson()) return response()->json(['status'=>'success']);
        set_alert('success', translate('information_has_been_deleted_successfully'));
        return back();
    }

    public function changePassword(Request $request)
    {
        abort_unless(get_permission('employee','is_edit'),403,translate('access_denied'));
        $rules = [];
        if (!$request->input('authentication')) $rules['password']=['required','min:4'];
        $request->validate($rules);
        $sid = $request->input('staff_id');
        if (!$request->input('authentication')) {
            DB::table('login_credential')->whereNotIn('role',[1,6,7])->where('user_id',$sid)->update(['password'=>Hash::make($request->input('password'))]);
        } else {
            DB::table('login_credential')->whereNotIn('role',[1,6,7])->where('user_id',$sid)->update(['active'=>0]);
        }
        set_alert('success', translate('information_has_been_updated_successfully'));
        return response()->json(['status'=>'success']);
    }

    /* bank */
    public function bankCreate(Request $request)
    {
        abort_unless(get_permission('employee','is_edit'),403,translate('access_denied'));
        $request->validate(['bank_name'=>['required'],'holder_name'=>['required'],'bank_branch'=>['required'],'account_no'=>['required']]);
        StaffBankAccount::create([
            'staff_id'=>$request->input('staff_id'),
            'bank_name'=>$request->input('bank_name'),
            'holder_name'=>$request->input('holder_name'),
            'bank_branch'=>$request->input('bank_branch'),
            'bank_address'=>$request->input('bank_address'),
            'ifsc_code'=>$request->input('ifsc_code'),
            'account_no'=>$request->input('account_no'),
        ]);
        set_alert('success', translate('information_has_been_saved_successfully'));
        return response()->json(['status'=>'success']);
    }

    public function bankUpdate(Request $request)
    {
        abort_unless(get_permission('employee','is_edit'),403,translate('access_denied'));
        $request->validate(['bank_name'=>['required'],'holder_name'=>['required'],'bank_branch'=>['required'],'account_no'=>['required']]);
        StaffBankAccount::where('id',$request->input('bank_id'))->update([
            'bank_name'=>$request->input('bank_name'),
            'holder_name'=>$request->input('holder_name'),
            'bank_branch'=>$request->input('bank_branch'),
            'bank_address'=>$request->input('bank_address'),
            'ifsc_code'=>$request->input('ifsc_code'),
            'account_no'=>$request->input('account_no'),
        ]);
        set_alert('success', translate('information_has_been_updated_successfully'));
        return response()->json(['status'=>'success']);
    }

    public function bankDelete($id)
    {
        abort_unless(get_permission('employee','is_edit'),403,translate('access_denied'));
        StaffBankAccount::where('id',$id)->delete();
        set_alert('success', translate('information_has_been_deleted_successfully'));
        return back();
    }

    public function bankDetails(Request $request)
    {
        $row = StaffBankAccount::where('id',$request->input('id'))->first();
        return response()->json($row);
    }

    /* documents */
    public function documentCreate(Request $request)
    {
        abort_unless(get_permission('employee','is_edit'),403,translate('access_denied'));
        $request->validate(['document_title'=>['required'],'document_category'=>['required']]);
        if (!$request->hasFile('document_file')) return response()->json(['status'=>'fail','error'=>['document_file'=>translate('document_file').' required']]);
        $file=$request->file('document_file');
        $dir=public_path('uploads/attachments/documents');
        if (!is_dir($dir)) mkdir($dir,0755,true);
        $enc=uniqid().'.'.$file->getClientOriginalExtension();
        $file->move($dir,$enc);
        StaffDocument::create([
            'staff_id'=>$request->input('staff_id'),
            'title'=>$request->input('document_title'),
            'category_id'=>$request->input('document_category'),
            'remarks'=>$request->input('remarks'),
            'file_name'=>$file->getClientOriginalName(),
            'enc_name'=>$enc,
        ]);
        set_alert('success', translate('information_has_been_saved_successfully'));
        return response()->json(['status'=>'success']);
    }

    public function documentUpdate(Request $request)
    {
        abort_unless(get_permission('employee','is_edit'),403,translate('access_denied'));
        $request->validate(['document_title'=>['required'],'document_category'=>['required']]);
        $id=$request->input('document_id');
        $data=['title'=>$request->input('document_title'),'category_id'=>$request->input('document_category'),'remarks'=>$request->input('remarks')];
        if ($request->hasFile('document_file')) {
            $file=$request->file('document_file');
            $dir=public_path('uploads/attachments/documents');
            if (!is_dir($dir)) mkdir($dir,0755,true);
            $old=$request->input('exist_file_name');
            if ($old && file_exists($dir.'/'.$old)) @unlink($dir.'/'.$old);
            $enc=uniqid().'.'.$file->getClientOriginalExtension();
            $file->move($dir,$enc);
            $data['file_name']=$file->getClientOriginalName();
            $data['enc_name']=$enc;
        }
        StaffDocument::where('id',$id)->update($data);
        set_alert('success', translate('information_has_been_updated_successfully'));
        return response()->json(['status'=>'success']);
    }

    public function documentDelete($id)
    {
        $doc=StaffDocument::where('id',$id)->first();
        if ($doc) {
            $path=public_path('uploads/attachments/documents/'.$doc->enc_name);
            if (file_exists($path)) @unlink($path);
            $doc->delete();
        }
        set_alert('success', translate('information_has_been_deleted_successfully'));
        return back();
    }

    public function documentDetails(Request $request)
    {
        return response()->json(StaffDocument::where('id',$request->input('id'))->first());
    }

    public function documentsDownload(Request $request)
    {
        $enc=$request->input('file') ?? $request->query('file');
        $doc=StaffDocument::where('enc_name',$enc)->first();
        if (!$doc) abort(404);
        $path=public_path('uploads/attachments/documents/'.$enc);
        if (!file_exists($path)) abort(404);
        return response()->download($path,$doc->file_name);
    }

    /* department */
    public function department(Request $request)
    {
        if ($request->isMethod('post')) {
            abort_unless(get_permission('department','is_add'),403,translate('access_denied'));
            $rules=['department_name'=>['required']];
            if (is_superadmin_loggedin()) $rules['branch_id']=['required'];
            $request->validate($rules);
            $branchID = $this->resolveBranchId($request);
            $exists = StaffDepartment::where('name',$request->input('department_name'))->where('branch_id',$branchID)->exists();
            if ($exists) return back()->withErrors(['department_name'=>translate('already_taken')])->withInput();
            StaffDepartment::create(['name'=>$request->input('department_name'),'branch_id'=>$branchID]);
            set_alert('success', translate('information_has_been_saved_successfully'));
            return redirect()->route('employee.department');
        }
        $departments = DB::table('staff_department as t')->leftJoin('branch as b','b.id','=','t.branch_id')->select('t.*','b.name as branch_name')->when(!is_superadmin_loggedin(), fn($q)=> $q->where('t.branch_id', get_loggedin_branch_id()))->orderBy('t.id','asc')->get();
        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();
        return view('employee.department', ['title'=>translate('employee'),'departments'=>$departments,'branches'=>$branches]);
    }

    public function departmentEdit(Request $request)
    {
        abort_unless(get_permission('department','is_edit'),403,translate('access_denied'));
        $request->validate(['department_name'=>['required']]);
        $branchID = $this->resolveBranchId($request);
        $id=$request->input('department_id');
        $exists = StaffDepartment::where('name',$request->input('department_name'))->where('branch_id',$branchID)->where('id','!=',$id)->exists();
        if ($exists) return response()->json(['status'=>'fail','error'=>['department_name'=>translate('already_taken')]]);
        StaffDepartment::where('id',$id)->update(['name'=>$request->input('department_name'),'branch_id'=>$branchID]);
        set_alert('success', translate('information_has_been_updated_successfully'));
        return response()->json(['status'=>'success']);
    }

    public function departmentDelete($id)
    {
        abort_unless(get_permission('department','is_delete'),403,translate('access_denied'));
        $q=StaffDepartment::where('id',$id);
        if (!is_superadmin_loggedin()) $q->where('branch_id', get_loggedin_branch_id());
        $q->delete();
        if (request()->expectsJson()) return response()->json(['status'=>'success']);
        set_alert('success', translate('information_has_been_deleted_successfully'));
        return back();
    }

    public function departmentDetails(Request $request)
    {
        $row = StaffDepartment::where('id',$request->input('id'))->first();
        return response()->json($row);
    }

    /* designation */
    public function designation(Request $request)
    {
        if ($request->isMethod('post')) {
            abort_unless(get_permission('designation','is_add'),403,translate('access_denied'));
            $rules=['designation_name'=>['required']];
            if (is_superadmin_loggedin()) $rules['branch_id']=['required'];
            $request->validate($rules);
            $branchID=$this->resolveBranchId($request);
            $exists = StaffDesignation::where('name',$request->input('designation_name'))->where('branch_id',$branchID)->exists();
            if ($exists) return back()->withErrors(['designation_name'=>translate('already_taken')])->withInput();
            StaffDesignation::create(['name'=>$request->input('designation_name'),'branch_id'=>$branchID]);
            set_alert('success', translate('information_has_been_saved_successfully'));
            return redirect()->route('employee.designation');
        }
        $designations = DB::table('staff_designation as t')->leftJoin('branch as b','b.id','=','t.branch_id')->select('t.*','b.name as branch_name')->when(!is_superadmin_loggedin(), fn($q)=> $q->where('t.branch_id', get_loggedin_branch_id()))->orderBy('t.id','asc')->get();
        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();
        return view('employee.designation', ['title'=>translate('employee'),'designations'=>$designations,'branches'=>$branches]);
    }

    public function designationEdit(Request $request)
    {
        abort_unless(get_permission('designation','is_edit'),403,translate('access_denied'));
        $request->validate(['designation_name'=>['required']]);
        $branchID=$this->resolveBranchId($request);
        $id=$request->input('designation_id');
        $exists = StaffDesignation::where('name',$request->input('designation_name'))->where('branch_id',$branchID)->where('id','!=',$id)->exists();
        if ($exists) return response()->json(['status'=>'fail','error'=>['designation_name'=>translate('already_taken')]]);
        StaffDesignation::where('id',$id)->update(['name'=>$request->input('designation_name'),'branch_id'=>$branchID]);
        set_alert('success', translate('information_has_been_updated_successfully'));
        return response()->json(['status'=>'success']);
    }

    public function designationDelete($id)
    {
        abort_unless(get_permission('designation','is_delete'),403,translate('access_denied'));
        StaffDesignation::where('id',$id)->delete();
        if (request()->expectsJson()) return response()->json(['status'=>'success']);
        set_alert('success', translate('information_has_been_deleted_successfully'));
        return back();
    }

    public function designationDetails(Request $request)
    {
        return response()->json(StaffDesignation::where('id',$request->input('id'))->first());
    }

    /* disable authentication */
    public function disableAuthentication(Request $request)
    {
        abort_unless(get_permission('disable_authentication','is_view') || get_permission('employee_disable_authentication','is_view'),403,translate('access_denied'));
        $stafflist=null;
        if ($request->isMethod('post') && $request->input('search')) {
            $branchID=$this->resolveBranchId($request);
            $role=$request->input('staff_role');
            $stafflist=$this->getStaffList($branchID,$role,0);
        }
        if ($request->isMethod('post') && $request->input('auth')) {
            abort_unless(get_permission('disable_authentication','is_add') || get_permission('employee_disable_authentication','is_add'),403,translate('access_denied'));
            $list=$request->input('views_bulk_operations');
            if (!empty($list)) {
                foreach ($list as $id) DB::table('login_credential')->where('user_id',$id)->whereNotIn('role',[1,6,7])->update(['active'=>1]);
                set_alert('success', translate('information_has_been_updated_successfully'));
            } else set_alert('error','Please select at least one item');
            return redirect()->route('employee.disable_authentication');
        }
        $roles = DB::table('roles')->whereNotIn('id',[1,6,7])->get();
        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();
        return view('employee.disable_authentication', ['title'=>translate('deactivate_account'),'stafflist'=>$stafflist,'roles'=>$roles,'branches'=>$branches]);
    }

    public function csvImport(Request $request)
    {
        $request->validate([
            'user_role'=>['required'],
            'designation_id'=>['required'],
            'department_id'=>['required'],
        ]);
        if (!$request->hasFile('userfile')) return response()->json(['status'=>'fail','error'=>['userfile'=>'Select CSV File']]);
        $branchID = $this->resolveBranchId($request);
        $userRole = $request->input('user_role');
        $desig = $request->input('designation_id');
        $dept = $request->input('department_id');
        $file=$request->file('userfile');
        $csv=array_map('str_getcsv', file($file->getRealPath()));
        $headers=array_shift($csv);
        $columnHeaders=['Name','Gender','Religion','BloodGroup','DateOfBirth','JoiningDate','Qualification','MobileNo','PresentAddress','PermanentAddress','Email','Password'];
        $diff=array_diff($columnHeaders,$headers);
        if(count($diff)>0) return response()->json(['status'=>'fail','error'=>['userfile'=>translate('invalid_csv_file')]]);
        $i=0;$err="";
        foreach($csv as $row){
            if(count($row)!=count($headers)) continue;
            $ra=array_combine($headers,$row);
            if(!filter_var($ra['Email'],FILTER_VALIDATE_EMAIL)){ $err.=$ra['Name']." - Imported Failed : Invalid Email.<br>"; continue; }
            if(DB::table('login_credential')->where('username',$ra['Email'])->exists()){ $err.=$ra['Name']." - Imported Failed : Email Already Exists.<br>"; continue; }
            $this->csvSave($ra,$branchID,$userRole,$desig,$dept);
            $i++;
        }
        if($err) return response()->json(['status'=>'errlist','errMsg'=> $i.' Students Have Been Successfully Added. <br>'.$err]);
        set_alert('success',$i.' Students Have Been Successfully Added');
        return response()->json(['status'=>'success']);
    }

    private function csvSave($row,$branchID,$userRole,$desig,$dept)
    {
        $staff=Staff::create([
            'name'=>$row['Name'],
            'sex'=>$row['Gender'],
            'religion'=>$row['Religion'],
            'blood_group'=>$row['BloodGroup'],
            'birthday'=>date('Y-m-d', strtotime($row['DateOfBirth'])),
            'joining_date'=>date('Y-m-d', strtotime($row['JoiningDate'])),
            'qualification'=>$row['Qualification'],
            'mobileno'=>$row['MobileNo'],
            'present_address'=>$row['PresentAddress'],
            'permanent_address'=>$row['PermanentAddress'],
            'email'=>$row['Email'],
            'designation'=>$desig,
            'department'=>$dept,
            'branch_id'=>$branchID,
            'photo'=>'defualt.png',
            'staff_id'=>substr(md5(rand().microtime().time().uniqid()),3,7),
            'hash'=>md5(rand().microtime().time().uniqid()),
        ]);
        User::create(['username'=>$row['Email'],'role'=>$userRole,'user_id'=>$staff->id,'password'=>Hash::make($row['Password']),'active'=>1]);
    }

    public function csvSampleDownload()
    {
        $path=public_path('uploads/multi_employee_sample.csv');
        if(!file_exists($path)) $path=base_path('legacy_code/uploads/multi_employee_sample.csv');
        if(!file_exists($path)) abort(404);
        return response()->download($path,'multi_employee_sample.csv');
    }
}
