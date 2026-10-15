<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Enroll;
use App\Models\ParentModel;
use App\Models\Student;
use App\Models\StudentCategory;
use App\Models\StudentDocument;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    private function resolveBranchId(Request $request): int
    {
        if (is_superadmin_loggedin()) {
            return (int) $request->input('branch_id', $request->input('branch_id_hidden', 0));
        }
        return (int) get_loggedin_branch_id();
    }

    private function isSectionEnabled($branchId): bool
    {
        if (empty($branchId)) return true;
        $row = Branch::where('id', $branchId)->first(['use_section']);
        return $row ? (bool)$row->use_section : true;
    }

    private function handlePhotoUpload(Request $request, $old = null, $role = 'student')
    {
        if ($request->hasFile('user_photo')) {
            $file = $request->file('user_photo');
            $ext = strtolower($file->getClientOriginalExtension());
            if (!in_array($ext, ['jpg','jpeg','png'])) {
                return null;
            }
            if ($file->getSize() > 2097152) return null;
            $dir = public_path('uploads/images/'.$role);
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            $filename = uniqid().'.'.$ext;
            $file->move($dir, $filename);
            if (!empty($old) && $old !== 'defualt.png' && file_exists($dir.'/'.$old)) {
                @unlink($dir.'/'.$old);
            }
            return $filename;
        }
        return $old ?? 'defualt.png';
    }

    /* ========== Admission: add ========== */
    public function create()
    {
        abort_unless(get_permission('student', 'is_add') || get_permission('admission', 'is_add'), 403, translate('access_denied'));
        $branchID = is_superadmin_loggedin() ? null : get_loggedin_branch_id();
        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();
        $classes = $branchID ? DB::table('class')->where('branch_id', $branchID)->get() : collect();
        $categories = $branchID ? StudentCategory::where('branch_id', $branchID)->get() : (is_superadmin_loggedin() ? StudentCategory::all() : collect());
        $parents = $branchID ? ParentModel::where('branch_id', $branchID)->get() : (is_superadmin_loggedin() ? ParentModel::all() : collect());
        // register id generation
        $prefix = '';
        $gs = DB::table('global_settings')->where('id',1)->first();
        if ($gs && isset($gs->reg_prefix) && $gs->reg_prefix=='on') $prefix = $gs->institution_code ?? '';
        $maxId = DB::table('student')->max('id');
        $maxNum = $maxId ? str_pad($maxId+1,5,'0',STR_PAD_LEFT) : '00001';
        $register_id = $prefix.$maxNum;

        return view('student.add', [
            'title'=>translate('create_admission'),
            'branch_id'=>$branchID,
            'branches'=>$branches,
            'classes'=>$classes,
            'categories'=>$categories,
            'parents'=>$parents,
            'register_id'=>$register_id,
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(get_permission('student', 'is_add') || get_permission('admission', 'is_add'), 403, translate('access_denied'));
        $branchID = $this->resolveBranchId($request);
        if (is_superadmin_loggedin() && empty($branchID)) {
            return back()->withErrors(['branch_id'=>translate('branch').' required'])->withInput();
        }
        $rules = [
            'year_id'=>['required'],
            'register_no'=>['required','string','max:100', Rule::unique('student','register_no')],
            'admission_date'=>['required'],
            'class_id'=>['required','integer'],
            'category_id'=>['required','integer'],
            'first_name'=>['required','string','max:255'],
            'last_name'=>['required','string','max:255'],
            'mobileno'=>['required'],
            'email'=>['required','email', Rule::unique('login_credential','username')],
            'password'=>['required','min:4'],
            'retype_password'=>['required','same:password'],
            'user_photo'=>['nullable','image','mimes:jpg,jpeg,png','max:2048'],
        ];
        if ($this->isSectionEnabled($branchID)) {
            $rules['section_id'] = ['required'];
        }
        $isExistingGuardian = $request->input('guardian_chk') ? true : false;
        if (!$isExistingGuardian) {
            $rules['grd_name'] = ['required'];
            $rules['grd_relation'] = ['required'];
            $rules['grd_occupation'] = ['required'];
            $rules['grd_mobileno'] = ['required'];
            $rules['grd_email'] = ['required','email', Rule::unique('login_credential','username')];
            $rules['grd_password'] = ['required','min:4'];
            $rules['grd_retype_password'] = ['required','same:grd_password'];
        } else {
            $rules['parent_id'] = ['required','integer','exists:parent,id'];
        }
        if (is_superadmin_loggedin()) $rules['branch_id'] = ['required','integer','exists:branch,id'];
        $validated = $request->validate($rules);

        // guardian
        if (!$isExistingGuardian) {
            $parent = ParentModel::create([
                'name'=>$request->input('grd_name'),
                'relation'=>$request->input('grd_relation'),
                'father_name'=>$request->input('father_name'),
                'mother_name'=>$request->input('mother_name'),
                'occupation'=>$request->input('grd_occupation'),
                'income'=>$request->input('grd_income'),
                'education'=>$request->input('grd_education'),
                'email'=>$request->input('grd_email'),
                'mobileno'=>$request->input('grd_mobileno'),
                'address'=>$request->input('grd_address'),
                'city'=>$request->input('grd_city'),
                'state'=>$request->input('grd_state'),
                'branch_id'=>$branchID,
                'photo'=>'defualt.png',
            ]);
            $parentId = $parent->id;
            User::create([
                'username'=>$request->input('grd_email'),
                'role'=>6,
                'user_id'=>$parentId,
                'password'=>Hash::make($request->input('grd_password')),
                'active'=>1,
            ]);
        } else {
            $parentId = $request->input('parent_id');
        }

        $photo = $this->handlePhotoUpload($request, null, 'student');
        $prev = json_encode([
            'school_name'=>$request->input('school_name'),
            'qualification'=>$request->input('qualification'),
            'remarks'=>$request->input('previous_remarks'),
        ]);
        $student = Student::create([
            'register_no'=>$request->input('register_no'),
            'admission_date'=>date('Y-m-d', strtotime($request->input('admission_date'))),
            'first_name'=>$request->input('first_name'),
            'last_name'=>$request->input('last_name'),
            'gender'=>$request->input('gender'),
            'birthday'=> $request->input('birthday') ? date('Y-m-d', strtotime($request->input('birthday'))) : null,
            'religion'=>$request->input('religion'),
            'caste'=>$request->input('caste') ?? '',
            'blood_group'=>$request->input('blood_group'),
            'mother_tongue'=>$request->input('mother_tongue') ?? '',
            'current_address'=>$request->input('current_address'),
            'permanent_address'=>$request->input('permanent_address'),
            'city'=>$request->input('city'),
            'state'=>$request->input('state'),
            'mobileno'=>$request->input('mobileno'),
            'category_id'=>$request->input('category_id'),
            'email'=>$request->input('email'),
            'parent_id'=>$parentId,
            'route_id'=>$request->input('route_id') ?? 0,
            'vehicle_id'=>$request->input('vehicle_id') ?? 0,
            'hostel_id'=>$request->input('hostel_id') ?? 0,
            'room_id'=>$request->input('room_id') ?? 0,
            'previous_details'=>$prev,
            'photo'=>$photo,
        ]);
        User::create([
            'username'=>$request->input('email'),
            'role'=>7,
            'user_id'=>$student->id,
            'password'=>Hash::make($request->input('password')),
            'active'=>1,
        ]);
        Enroll::create([
            'student_id'=>$student->id,
            'class_id'=>$request->input('class_id'),
            'section_id'=>$request->input('section_id') ?? 0,
            'roll'=>$request->input('roll') ?? 0,
            'session_id'=>$request->input('year_id'),
            'branch_id'=>$branchID,
        ]);
        set_alert('success', translate('information_has_been_saved_successfully'));
        return redirect()->route('student.create');
    }

    /* ========== View / List ========== */
    public function index(Request $request)
    {
        abort_unless(get_permission('student', 'is_view'), 403, translate('access_denied'));
        $branchID = is_superadmin_loggedin() ? null : get_loggedin_branch_id();
        // Simple list without filter: show active students
        $query = DB::table('enroll as e')
            ->join('student as s','s.id','=','e.student_id')
            ->join('login_credential as l', function($j){ $j->on('l.user_id','=','s.id')->where('l.role','=',7); })
            ->leftJoin('class as c','c.id','=','e.class_id')
            ->leftJoin('section as se','se.id','=','e.section_id')
            ->select('e.*','e.id as enroll_id','s.photo','s.id as student_id','s.first_name','s.last_name', DB::raw('CONCAT(s.first_name," ",s.last_name) as fullname'),'s.register_no','s.parent_id','s.email','s.mobileno','s.blood_group','s.birthday','s.admission_date','l.active','c.name as class_name','se.name as section_name')
            ->where('l.active',1)
            ->orderBy('s.id','asc');
        if (!empty($branchID)) $query->where('e.branch_id',$branchID);
        $students = $query->get()->toArray();
        // for display also handle array
        $studentsArr = array_map(fn($r)=>(array)$r, $students);
        return view('student.index', [
            'title'=>translate('student_list'),
            'students'=>$studentsArr,
            'branch_id'=>$branchID,
        ]);
    }

    /* ========== Profile (edit) ========== */
    public function profile($id, Request $request)
    {
        abort_unless(get_permission('student', 'is_edit'), 403, translate('access_denied'));
        $studentData = $this->getSingleStudent($id);
        abort_if(!$studentData, 404);
        if ($request->isMethod('post') || $request->input('update')) {
            // validation similar to add but with unique ignores
            $branchID = is_superadmin_loggedin() ? ($request->input('branch_id') ?? $studentData['branch_id']) : get_loggedin_branch_id();
            $rules = [
                'year_id'=>['required'],
                'register_no'=>['required', Rule::unique('student','register_no')->ignore($id)],
                'admission_date'=>['required'],
                'class_id'=>['required'],
                'category_id'=>['required'],
                'first_name'=>['required'],
                'last_name'=>['required'],
                'mobileno'=>['required'],
                'email'=>['required','email', Rule::unique('login_credential','username')->where(fn($q)=> $q->where('role',7)->where('user_id','!=',$id))],
                'parent_id'=>['required'],
            ];
            if ($this->isSectionEnabled($branchID)) $rules['section_id']=['required'];
            $request->validate($rules);
            $photo = $this->handlePhotoUpload($request, $studentData['photo'], 'student');
            $prev = json_encode([
                'school_name'=>$request->input('school_name'),
                'qualification'=>$request->input('qualification'),
                'remarks'=>$request->input('previous_remarks'),
            ]);
            Student::where('id',$id)->update([
                'register_no'=>$request->input('register_no'),
                'admission_date'=>date('Y-m-d', strtotime($request->input('admission_date'))),
                'first_name'=>$request->input('first_name'),
                'last_name'=>$request->input('last_name'),
                'gender'=>$request->input('gender'),
                'birthday'=> $request->input('birthday') ? date('Y-m-d', strtotime($request->input('birthday'))) : null,
                'religion'=>$request->input('religion'),
                'caste'=> $request->input('caste') ?? '',
                'blood_group'=>$request->input('blood_group'),
                'mother_tongue'=> $request->input('mother_tongue') ?? '',
                'current_address'=>$request->input('current_address'),
                'permanent_address'=>$request->input('permanent_address'),
                'city'=>$request->input('city'),
                'state'=>$request->input('state'),
                'mobileno'=>$request->input('mobileno'),
                'category_id'=>$request->input('category_id'),
                'email'=>$request->input('email'),
                'parent_id'=>$request->input('parent_id'),
                'route_id'=>$request->input('route_id') ?? 0,
                'vehicle_id'=>$request->input('vehicle_id') ?? 0,
                'hostel_id'=>$request->input('hostel_id') ?? 0,
                'room_id'=>$request->input('room_id') ?? 0,
                'previous_details'=>$prev,
                'photo'=>$photo,
            ]);
            DB::table('login_credential')->where('user_id',$id)->where('role',7)->update(['username'=>$request->input('email')]);
            Enroll::where('student_id',$id)->update([
                'class_id'=>$request->input('class_id'),
                'section_id'=>$request->input('section_id') ?? 0,
                'roll'=>$request->input('roll') ?? 0,
                'session_id'=>$request->input('year_id'),
                'branch_id'=>$branchID,
            ]);
            set_alert('success', translate('information_has_been_updated_successfully'));
            return redirect()->route('student.profile', $id);
        }
        $branchID = is_superadmin_loggedin() ? $studentData['branch_id'] : get_loggedin_branch_id();
        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();
        $classes = $branchID ? DB::table('class')->where('branch_id',$branchID)->get() : collect();
        $categories = $branchID ? StudentCategory::where('branch_id',$branchID)->get() : collect();
        $parents = $branchID ? ParentModel::where('branch_id',$branchID)->get() : collect();
        $sections = [];
        if (!empty($studentData['class_id'])) {
            $sections = DB::table('sections_allocation')->join('section','section.id','=','sections_allocation.section_id')->where('sections_allocation.class_id',$studentData['class_id'])->select('section.id','section.name')->get();
        }
        return view('student.profile', [
            'title'=>translate('student_profile'),
            'student'=>$studentData,
            'branches'=>$branches,
            'classes'=>$classes,
            'categories'=>$categories,
            'parents'=>$parents,
            'sections'=>$sections,
            'branch_id'=>$branchID,
        ]);
    }

    private function getSingleStudent($id)
    {
        $q = DB::table('student as s')
            ->leftJoin('enroll as e','e.student_id','=','s.id')
            ->leftJoin('login_credential as l', function($j){ $j->on('l.user_id','=','s.id')->where('l.role','=',7); })
            ->leftJoin('class as c','c.id','=','e.class_id')
            ->leftJoin('section as se','se.id','=','e.section_id')
            ->leftJoin('student_category as sc','sc.id','=','s.category_id')
            ->select('s.*','e.class_id','e.section_id','e.id as enrollid','e.roll','e.branch_id','e.session_id','c.name as class_name','se.name as section_name','sc.name as category_name','l.active')
            ->where('s.id',$id);
        if (!is_superadmin_loggedin()) $q->where('e.branch_id', get_loggedin_branch_id());
        $row = $q->first();
        return $row ? (array)$row : null;
    }

    /* ========== Delete ========== */
    public function delete(Request $request, $eid = null, $sid = null)
    {
        abort_unless(get_permission('student', 'is_delete'), 403, translate('access_denied'));
        // Supports both REST delete with single id and legacy eid/sid
        if ($request->has('array_id')) {
            $ids = $request->input('array_id');
            DB::table('enroll')->whereIn('student_id',$ids)->delete();
            DB::table('student')->whereIn('id',$ids)->delete();
            DB::table('login_credential')->whereIn('user_id',$ids)->where('role',7)->delete();
            return response()->json(['status'=>'success','message'=>translate('information_deleted')]);
        }
        $enrollId = $eid ?? $request->input('enroll_id') ?? $request->route('eid');
        $studentId = $sid ?? $request->input('student_id') ?? $request->route('sid');
        if ($enrollId && $studentId) {
            $q = DB::table('enroll')->where('id',$enrollId);
            if (!is_superadmin_loggedin()) $q->where('branch_id', get_loggedin_branch_id());
            $q->delete();
            if (DB::table('enroll')->where('id',$enrollId)->count()==0) {
                // deleted
                DB::table('student')->where('id',$studentId)->delete();
                DB::table('login_credential')->where('user_id',$studentId)->where('role',7)->delete();
            }
            if ($request->expectsJson()) return response()->json(['status'=>'success']);
            return back();
        }
        // alternative: delete by student id via route param {id}
        $id = $request->route('id') ?? $eid;
        if ($id) {
            DB::table('enroll')->where('student_id',$id)->delete();
            DB::table('student')->where('id',$id)->delete();
            DB::table('login_credential')->where('user_id',$id)->where('role',7)->delete();
            if ($request->expectsJson()) return response()->json(['status'=>'success']);
            set_alert('success', translate('information_has_been_deleted_successfully'));
            return back();
        }
        abort(404);
    }

    public function bulkDelete(Request $request)
    {
        abort_unless(get_permission('student', 'is_delete'), 403, translate('access_denied'));
        $ids = $request->input('array_id');
        if (empty($ids)) return response()->json(['status'=>'error','message'=>translate('no_information_available')]);
        DB::table('enroll')->whereIn('student_id',$ids)->delete();
        DB::table('student')->whereIn('id',$ids)->delete();
        DB::table('login_credential')->whereIn('user_id',$ids)->where('role',7)->delete();
        return response()->json(['status'=>'success','message'=>translate('information_deleted')]);
    }

    /* ========== CSV import ========== */
    public function csvImport(Request $request)
    {
        abort_unless(get_permission('multiple_import','is_add') || get_permission('admission','is_add'), 403, translate('access_denied'));
        $branchID = is_superadmin_loggedin() ? null : get_loggedin_branch_id();
        if ($request->isMethod('post')) {
            $rules = [
                'class_id'=>['required'],
                'section_id'=>['required'],
            ];
            if (is_superadmin_loggedin()) $rules['branch_id']=['required'];
            if (!$request->hasFile('userfile')) $rules['userfile']=['required'];
            $request->validate($rules);
            if (is_superadmin_loggedin()) $branchID = $request->input('branch_id');
            $classID = $request->input('class_id');
            $sectionID = $request->input('section_id');
            $file = $request->file('userfile');
            $csv = array_map('str_getcsv', file($file->getRealPath()));
            $headers = array_shift($csv);
            // expected headers
            $columnHeaders = ['FirstName','LastName','BloodGroup','Gender','Birthday','MotherTongue','Religion','Caste','Phone','City','State','PresentAddress','PermanentAddress','CategoryID','Roll','AdmissionDate','StudentEmail','StudentPassword','GuardianName','GuardianRelation','FatherName','MotherName','GuardianOccupation','GuardianMobileNo','GuardianAddress','GuardianEmail','GuardianPassword'];
            $diff = array_diff($columnHeaders, $headers);
            if (count($diff)>0) {
                set_alert('error', translate('invalid_csv_file'));
                return back();
            }
            $i=0; $err_msg="";
            foreach ($csv as $row) {
                if (count($row) != count($headers)) continue;
                $rowAssoc = array_combine($headers, $row);
                if (!filter_var($rowAssoc['StudentEmail'], FILTER_VALIDATE_EMAIL)) {
                    $err_msg .= $rowAssoc['FirstName'].' '.$rowAssoc['LastName']." - Imported Failed : Invalid Email.<br>";
                    continue;
                }
                // check exists
                $existsRoll = DB::table('enroll')->where(['roll'=>$rowAssoc['Roll'],'class_id'=>$classID,'branch_id'=>$branchID])->exists();
                if ($existsRoll) {
                    $err_msg .= $rowAssoc['FirstName'].' '.$rowAssoc['LastName']." - Imported Failed : Roll Already Exists.<br>";
                    continue;
                }
                $existsEmail = DB::table('login_credential')->where('username',$rowAssoc['StudentEmail'])->exists();
                if ($existsEmail) {
                    $err_msg .= $rowAssoc['FirstName'].' '.$rowAssoc['LastName']." - Imported Failed : Email Already Exists.<br>";
                    continue;
                }
                $this->csvSave($rowAssoc, $classID, $sectionID, $branchID);
                $i++;
            }
            if ($err_msg) session()->flash('csvimport',$err_msg);
            if ($i>0) set_alert('success', $i.' Students Have Been Successfully Added');
            return redirect()->route('student.csv_import');
        }
        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();
        $classes = $branchID ? DB::table('class')->where('branch_id',$branchID)->get() : collect();
        return view('student.csv_import', [
            'title'=>translate('multiple_import'),
            'branch_id'=>$branchID,
            'branches'=>$branches,
            'classes'=>$classes,
        ]);
    }

    private function csvSave($row, $classID, $sectionID, $branchID)
    {
        $getParent = DB::table('parent')->where(['branch_id'=>$branchID,'email'=>$row['GuardianEmail']])->first();
        if ($getParent) {
            $parentID = $getParent->id;
        } else {
            $parentID = DB::table('parent')->insertGetId([
                'name'=>$row['GuardianName'],
                'relation'=>$row['GuardianRelation'],
                'father_name'=>$row['FatherName'],
                'mother_name'=>$row['MotherName'],
                'occupation'=>$row['GuardianOccupation'],
                'mobileno'=>$row['GuardianMobileNo'],
                'address'=>$row['GuardianAddress'],
                'email'=>$row['GuardianEmail'],
                'branch_id'=>$branchID,
                'photo'=>'defualt.png',
                'created_at'=>now(),'updated_at'=>now(),
            ]);
            DB::table('login_credential')->insert([
                'username'=>$row['GuardianEmail'],
                'role'=>6,
                'user_id'=>$parentID,
                'password'=>Hash::make($row['GuardianPassword']),
                'active'=>1,
                'created_at'=>now(),'updated_at'=>now(),
            ]);
        }
        $studentId = DB::table('student')->insertGetId([
            'first_name'=>$row['FirstName'],
            'last_name'=>$row['LastName'],
            'blood_group'=>$row['BloodGroup'],
            'gender'=>$row['Gender'],
            'birthday'=> date('Y-m-d', strtotime($row['Birthday'])),
            'mother_tongue'=>$row['MotherTongue'],
            'religion'=>$row['Religion'],
            'parent_id'=>$parentID,
            'caste'=>$row['Caste'],
            'mobileno'=>$row['Phone'],
            'city'=>$row['City'],
            'state'=>$row['State'],
            'current_address'=>$row['PresentAddress'],
            'permanent_address'=>$row['PermanentAddress'],
            'category_id'=>$row['CategoryID'],
            'admission_date'=>date('Y-m-d', strtotime($row['AdmissionDate'])),
            'register_no'=>substr(md5(rand().microtime().time().uniqid()),4,7),
            'photo'=>'defualt.png',
            'email'=>$row['StudentEmail'],
            'route_id'=>0,'vehicle_id'=>0,'hostel_id'=>0,'room_id'=>0,
            'previous_details'=>json_encode([]),
            'created_at'=>now(),'updated_at'=>now(),
        ]);
        DB::table('login_credential')->insert([
            'username'=>$row['StudentEmail'],
            'role'=>7,
            'user_id'=>$studentId,
            'password'=>Hash::make($row['StudentPassword']),
            'active'=>1,
            'created_at'=>now(),'updated_at'=>now(),
        ]);
        DB::table('enroll')->insert([
            'student_id'=>$studentId,
            'class_id'=>$classID,
            'section_id'=>$sectionID,
            'branch_id'=>$branchID,
            'roll'=>$row['Roll'],
            'session_id'=>get_session_id(),
            'created_at'=>now(),'updated_at'=>now(),
        ]);
    }

    public function csvSampleDownload()
    {
        $path = public_path('uploads/multi_student_sample.csv');
        if (!file_exists($path)) {
            // try legacy path
            $path2 = base_path('legacy_code/uploads/multi_student_sample.csv');
            if (file_exists($path2)) $path=$path2;
        }
        if (!file_exists($path)) abort(404);
        return response()->download($path, 'multi_student_sample.csv');
    }

    /* ========== Category ========== */
    public function category(Request $request)
    {
        abort_unless(get_permission('student_category','is_view'), 403, translate('access_denied'));
        if ($request->isMethod('post') && $request->input('category')) {
            abort_unless(get_permission('student_category','is_add'), 403, translate('access_denied'));
            $rules = ['category_name'=>['required','string','max:255']];
            if (is_superadmin_loggedin()) $rules['branch_id']=['required'];
            $request->validate($rules);
            $branchID = $this->resolveBranchId($request);
            // unique check
            $exists = StudentCategory::where('name',$request->input('category_name'))->where('branch_id',$branchID)->exists();
            if ($exists) {
                return back()->withErrors(['category_name'=>translate('already_taken')])->withInput();
            }
            StudentCategory::create(['name'=>$request->input('category_name'),'branch_id'=>$branchID]);
            set_alert('success', translate('information_has_been_saved_successfully'));
            return redirect()->route('student.category');
        }
        $branchID = is_superadmin_loggedin() ? null : get_loggedin_branch_id();
        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();
        $query = StudentCategory::query();
        if (!is_superadmin_loggedin()) $query->where('branch_id', get_loggedin_branch_id());
        $categories = $query->get();
        return view('student.category', [
            'title'=>translate('student')." ".translate('details'),
            'categories'=>$categories,
            'branches'=>$branches,
            'branch_id'=>$branchID,
        ]);
    }

    public function categoryEdit(Request $request)
    {
        abort_unless(get_permission('student_category','is_edit'), 403, translate('access_denied'));
        $rules = ['category_name'=>['required']];
        if (is_superadmin_loggedin()) $rules['branch_id']=['required'];
        $validated = $request->validate($rules);
        $branchID = $this->resolveBranchId($request);
        $id = $request->input('category_id');
        $exists = StudentCategory::where('name',$request->input('category_name'))->where('branch_id',$branchID)->where('id','!=',$id)->exists();
        if ($exists) {
            return response()->json(['status'=>'fail','error'=>['category_name'=>translate('already_taken')]]);
        }
        StudentCategory::where('id',$id)->update(['name'=>$request->input('category_name'),'branch_id'=>$branchID]);
        set_alert('success', translate('information_has_been_updated_successfully'));
        return response()->json(['status'=>'success']);
    }

    public function categoryDelete($id)
    {
        abort_unless(get_permission('student_category','is_delete'), 403, translate('access_denied'));
        $q = StudentCategory::where('id',$id);
        if (!is_superadmin_loggedin()) $q->where('branch_id', get_loggedin_branch_id());
        $q->delete();
        if (request()->expectsJson()) return response()->json(['status'=>'success']);
        set_alert('success', translate('information_has_been_deleted_successfully'));
        return back();
    }

    public function categoryDetails(Request $request)
    {
        abort_unless(get_permission('student_category','is_edit'), 403, translate('access_denied'));
        $id = $request->input('id');
        $q = StudentCategory::where('id',$id);
        if (!is_superadmin_loggedin()) $q->where('branch_id', get_loggedin_branch_id());
        $row = $q->first();
        return response()->json($row);
    }

    /* ========== Disable authentication ========== */
    public function disableAuthentication(Request $request)
    {
        abort_unless(get_permission('disable_authentication','is_view') || get_permission('student_disable_authentication','is_view'), 403, translate('access_denied'));
        $branchID = $this->resolveBranchId($request);
        $students = null;
        if ($request->isMethod('post') && $request->input('search')) {
            $classID = $request->input('class_id');
            $sectionID = $request->input('section_id');
            $students = $this->getStudentListByClassSection($classID,$sectionID,$branchID,true);
        }
        if ($request->isMethod('post') && $request->input('auth')) {
            abort_unless(get_permission('disable_authentication','is_add') || get_permission('student_disable_authentication','is_add'), 403, translate('access_denied'));
            $list = $request->input('views_bulk_operations');
            if (!empty($list)) {
                foreach ($list as $id) {
                    DB::table('login_credential')->where(['role'=>7,'user_id'=>$id])->update(['active'=>1]);
                }
                set_alert('success', translate('information_has_been_updated_successfully'));
            } else {
                set_alert('error','Please select at least one item');
            }
            return redirect()->route('student.disable_authentication');
        }
        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();
        $classes = $branchID ? DB::table('class')->where('branch_id',$branchID)->get() : collect();
        return view('student.disable_authentication', [
            'title'=>translate('deactivate_account'),
            'branch_id'=>$branchID,
            'branches'=>$branches,
            'classes'=>$classes,
            'students'=>$students,
        ]);
    }

    private function getStudentListByClassSection($classID,$sectionID,$branchID,$deactivate=false,$rollOrder=false)
    {
        $q = DB::table('enroll as e')->join('student as s','s.id','=','e.student_id')->join('login_credential as l', function($j){$j->on('l.user_id','=','s.id')->where('l.role','=',7);})->leftJoin('class as c','c.id','=','e.class_id')->leftJoin('section as se','se.id','=','e.section_id')->select('e.*','s.photo', DB::raw('CONCAT(s.first_name," ",s.last_name) as fullname'),'s.register_no','s.parent_id','s.email','l.active','c.name as class_name','se.name as section_name');
        if (!empty($classID)) $q->where('e.class_id',$classID);
        if (!empty($branchID)) $q->where('e.branch_id',$branchID);
        if (!empty($sectionID) && $sectionID!='all') $q->where('e.section_id',$sectionID);
        $q->where('l.active', $deactivate?0:1);
        $q->orderBy($rollOrder ? 'e.roll':'s.id','asc');
        return $q->get();
    }

    /* ========== ID card ========== */
    public function generateIdcard(Request $request)
    {
        abort_unless(get_permission('id_card','is_view') || get_permission('student_id_card','is_view'), 403, translate('access_denied'));
        $branchID = $this->resolveBranchId($request);
        $students = null;
        if ($request->isMethod('post')) {
            $classID = $request->input('class_id');
            $sectionID = $request->input('section_id');
            $q = DB::table('enroll as e')->join('student as s','s.id','=','e.student_id')->join('login_credential as l', function($j){$j->on('l.user_id','=','s.id')->where('l.role','=',7);})->leftJoin('class as c','c.id','=','e.class_id')->leftJoin('section as se','se.id','=','e.section_id')->select('e.*','s.photo', DB::raw('CONCAT(s.first_name," ",s.last_name) as fullname'),'s.register_no','s.parent_id','s.email','s.blood_group','s.birthday','l.active','c.name as class_name','se.name as section_name')->where('e.class_id',$classID)->where('e.branch_id',$branchID)->where('l.active',1);
            if ($sectionID != 'all' && !empty($sectionID)) $q->where('e.section_id',$sectionID);
            $students = $q->get();
            // add qr data (simple md5 image placeholder)
            foreach ($students as $row) {
                $row->qr_code = md5($row->register_no).'.png';
            }
        }
        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();
        $branchForSelect = $branchID ?: (is_superadmin_loggedin()? null : get_loggedin_branch_id());
        $classes = $branchForSelect ? DB::table('class')->where('branch_id',$branchForSelect)->get() : collect();
        return view('student.idcard', [
            'title'=>translate('id_card_generate'),
            'branch_id'=>$branchForSelect,
            'branches'=>$branches,
            'classes'=>$classes,
            'students'=>$students,
        ]);
    }

    /* ========== Search ========== */
    public function search(Request $request)
    {
        abort_unless(get_permission('student','is_view'), 403, translate('access_denied'));
        $search = $request->input('search_text');
        if (empty($search)) return back();
        $q = DB::table('enroll as e')->join('student as s','s.id','=','e.student_id')->leftJoin('class as c','c.id','=','e.class_id')->leftJoin('section as se','se.id','=','e.section_id')->leftJoin('parent as sp','sp.id','=','s.parent_id')->join('login_credential as l', function($j){$j->on('l.user_id','=','s.id')->where('l.role','=',7);})->select('e.*','s.photo','s.first_name','s.last_name','s.register_no','s.parent_id','s.email','s.blood_group','s.birthday','c.name as class_name','se.name as section_name','sp.name as parent_name')->where('l.active',1);
        if (!is_superadmin_loggedin()) $q->where('e.branch_id', get_loggedin_branch_id());
        $q->where(function($qq) use($search){
            $qq->where('s.first_name','like',"%$search%")->orWhere('s.last_name','like',"%$search%")->orWhere('s.register_no','like',"%$search%")->orWhere('s.email','like',"%$search%")->orWhere('e.roll','like',"%$search%")->orWhere('s.blood_group','like',"%$search%")->orWhere('sp.name','like',"%$search%");
        });
        $results = $q->orderBy('s.id','desc')->get();
        return view('student.search', [
            'title'=>translate('searching_results'),
            'query'=>$results,
            'search_text'=>$search,
        ]);
    }

    /* ========== Change password / deactivate ========== */
    public function changePassword(Request $request)
    {
        abort_unless(get_permission('student','is_edit'), 403, translate('access_denied'));
        $rules = [];
        if (!$request->input('authentication')) $rules['password']=['required','min:4'];
        $request->validate($rules);
        $studentID = $request->input('student_id');
        if (!$request->input('authentication')) {
            DB::table('login_credential')->where('role',7)->where('user_id',$studentID)->update(['password'=>Hash::make($request->input('password'))]);
        } else {
            DB::table('login_credential')->where('role',7)->where('user_id',$studentID)->update(['active'=>0]);
        }
        set_alert('success', translate('information_has_been_updated_successfully'));
        return response()->json(['status'=>'success']);
    }

    /* ========== Documents ========== */
    public function documentCreate(Request $request)
    {
        abort_unless(get_permission('student','is_edit'), 403, translate('access_denied'));
        $request->validate(['document_title'=>['required'],'document_category'=>['required']]);
        if (!$request->hasFile('document_file')) return response()->json(['status'=>'fail','error'=>['document_file'=>translate('document_file').' required']]);
        $file = $request->file('document_file');
        $dir = public_path('uploads/attachments/documents');
        if (!is_dir($dir)) mkdir($dir,0755,true);
        $enc = uniqid().'.'.$file->getClientOriginalExtension();
        $file->move($dir, $enc);
        StudentDocument::create([
            'student_id'=>$request->input('patient_id') ?? $request->input('student_id'),
            'title'=>$request->input('document_title'),
            'type'=>$request->input('document_category'),
            'remarks'=>$request->input('remarks'),
            'file_name'=>$file->getClientOriginalName(),
            'enc_name'=>$enc,
        ]);
        set_alert('success', translate('information_has_been_saved_successfully'));
        return response()->json(['status'=>'success']);
    }

    public function documentUpdate(Request $request)
    {
        abort_unless(get_permission('student','is_edit'), 403, translate('access_denied'));
        $request->validate(['document_title'=>['required'],'document_category'=>['required']]);
        $id = $request->input('document_id');
        $data = ['title'=>$request->input('document_title'),'type'=>$request->input('document_category'),'remarks'=>$request->input('remarks')];
        if ($request->hasFile('document_file')) {
            $file = $request->file('document_file');
            $dir = public_path('uploads/attachments/documents');
            if (!is_dir($dir)) mkdir($dir,0755,true);
            $old = $request->input('exist_file_name');
            if ($old && file_exists($dir.'/'.$old)) @unlink($dir.'/'.$old);
            $enc = uniqid().'.'.$file->getClientOriginalExtension();
            $file->move($dir,$enc);
            $data['file_name']=$file->getClientOriginalName();
            $data['enc_name']=$enc;
        }
        StudentDocument::where('id',$id)->update($data);
        set_alert('success', translate('information_has_been_updated_successfully'));
        return response()->json(['status'=>'success']);
    }

    public function documentDelete($id)
    {
        abort_unless(get_permission('student','is_edit'), 403, translate('access_denied'));
        $doc = StudentDocument::where('id',$id)->first();
        if ($doc) {
            $path = public_path('uploads/attachments/documents/'.$doc->enc_name);
            if (file_exists($path)) @unlink($path);
            $doc->delete();
        }
        set_alert('success', translate('information_has_been_deleted_successfully'));
        return back();
    }

    public function documentDetails(Request $request)
    {
        $id = $request->input('id');
        $row = StudentDocument::where('id',$id)->first();
        return response()->json($row);
    }

    public function documentsDownload(Request $request)
    {
        $enc = $request->input('file') ?? $request->query('file');
        $doc = StudentDocument::where('enc_name',$enc)->first();
        if (!$doc) abort(404);
        $path = public_path('uploads/attachments/documents/'.$enc);
        if (!file_exists($path)) abort(404);
        return response()->download($path, $doc->file_name);
    }

    /* ========== Transfer / Promotion ========== */
    public function transfer(Request $request)
    {
        abort_unless(get_permission('student_promotion','is_view'), 403, translate('access_denied'));
        $branchID = $this->resolveBranchId($request);
        $students = null;
        $data = [];
        if ($request->isMethod('post')) {
            $classID = $request->input('class_id');
            $sectionID = $request->input('section_id');
            $data['class_id']=$classID;
            $data['section_id']=$sectionID;
            $students = $this->getStudentListByClassSection($classID,$sectionID,$branchID,false,true);
        }
        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();
        $classes = $branchID ? DB::table('class')->where('branch_id',$branchID)->get() : collect();
        return view('student.transfer', [
            'title'=>translate('student_promotion'),
            'branch_id'=>$branchID,
            'branches'=>$branches,
            'classes'=>$classes,
            'students'=>$students,
            'data'=>$data,
        ]);
    }

    public function transferSave(Request $request)
    {
        abort_unless(get_permission('student_promotion','is_add'), 403, translate('access_denied'));
        $request->validate([
            'promote_session_id'=>['required'],
            'promote_class_id'=>['required'],
            'promote_section_id'=>['required'],
        ]);
        $promote = $request->input('promote');
        if (empty($promote)) return response()->json(['status'=>'fail','error'=>['promote'=>translate('no_information_available')]]);
        // validate roll uniqueness
        foreach ($promote as $key=>$value) {
            if (isset($value['enroll_id'])) {
                $roll = $value['roll'];
                $exists = DB::table('enroll')->where(['roll'=>$roll,'class_id'=>$request->input('promote_class_id'),'session_id'=>$request->input('promote_session_id'),'branch_id'=>$this->resolveBranchId($request)])->exists();
                if ($exists) {
                    // check if same enroll being updated to same roll may count as duplicate - legacy lacked exclusion, we keep simple
                    return response()->json(['status'=>'fail','error'=>["promote.$key.roll"=>"The roll is already exists."]]);
                }
            }
        }
        $branchID = $this->resolveBranchId($request);
        $promote_session_id = $request->input('promote_session_id');
        $promote_class_id = $request->input('promote_class_id');
        $promote_section_id = $request->input('promote_section_id');
        foreach ($promote as $key=>$value) {
            if (isset($value['enroll_id'])) {
                $roll = $value['roll'];
                $enroll_id = $value['enroll_id'];
                $exists = DB::table('enroll')->where('student_id',$value['student_id'])->where('session_id',$promote_session_id)->first();
                $arrayData = [
                    'student_id'=>$value['student_id'],
                    'class_id'=>$promote_class_id,
                    'roll'=>$roll,
                    'section_id'=>$promote_section_id,
                    'session_id'=>$promote_session_id,
                    'branch_id'=>$branchID,
                ];
                if ($exists) {
                    DB::table('enroll')->where('id',$enroll_id)->update($arrayData);
                } else {
                    DB::table('enroll')->insert(array_merge($arrayData, ['created_at'=>now(),'updated_at'=>now()]));
                }
            }
        }
        set_alert('success', translate('information_has_been_updated_successfully'));
        return response()->json(['status'=>'success','url'=>route('student.transfer')]);
    }

    public function quickDetails(Request $request)
    {
        $id = $request->input('student_id') ?? $request->input('id');
        $row = DB::table('enroll as e')->join('student as s','s.id','=','e.student_id')->join('student_category as sc','sc.id','=','s.category_id')->where('e.id',$id)->select('s.*','e.roll','e.student_id','sc.name as cname')->first();
        if (!$row) return response()->json([]);
        return response()->json([
            'photo'=> get_image_url('student',$row->photo),
            'full_name'=>$row->first_name." ".$row->last_name,
            'student_category'=>$row->cname,
            'register_no'=>$row->register_no,
            'roll'=>$row->roll,
            'admission_date'=> empty($row->admission_date)?"N/A":_d($row->admission_date),
            'birthday'=> empty($row->birthday)?"N/A":_d($row->birthday),
            'blood_group'=> empty($row->blood_group)?"N/A":$row->blood_group,
            'religion'=> empty($row->religion)?"N/A":$row->religion,
            'email'=>$row->email,
            'mobileno'=> empty($row->mobileno)?"N/A":$row->mobileno,
            'state'=> empty($row->state)?"N/A":$row->state,
            'address'=> empty($row->current_address)?"N/A":$row->current_address,
        ]);
    }
}
