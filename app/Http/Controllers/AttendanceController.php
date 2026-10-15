<?php
namespace App\Http\Controllers;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class AttendanceController extends Controller
{
    private function resolveBranchId(Request $request): int
    {
        if(is_superadmin_loggedin()){ $bid=$request->input('branch_id'); if($bid) return (int)$bid; return (int)(DB::table('branch')->value('id') ?? 0);}
        return (int)get_loggedin_branch_id();
    }
    public function student(Request $request)
    {
        abort_unless(get_permission('student_attendance','is_view') || get_permission('student_attendance','is_add'),403);
        $branchId=$this->resolveBranchId($request);
        $branches=is_superadmin_loggedin()?Branch::orderBy('name')->get():collect();
        $classes=$branchId?DB::table('class')->where('branch_id',$branchId)->get():collect();
        $attendencelist=null; $date=null;
        if($request->isMethod('post') && $request->has('search')){
            $rules=['class_id'=>['required'],'section_id'=>['required'],'date'=>['required','date']];
            if(is_superadmin_loggedin()) $rules['branch_id']=['required'];
            $validated=$request->validate($rules);
            if(strtotime($validated['date']) > strtotime(date('Y-m-d'))) return back()->withErrors(['date'=>'Please Enter Correct Date'])->withInput();
            $classID=$validated['class_id']; $sectionID=$validated['section_id']; $date=$validated['date'];
            $sql="SELECT enroll.student_id,enroll.roll,student.first_name,student.last_name,student.register_no,student.photo,student_attendance.id as att_id, student_attendance.status as att_status,student_attendance.remark as att_remark FROM enroll LEFT JOIN student ON student.id = enroll.student_id LEFT JOIN student_attendance ON student_attendance.student_id = student.id AND student_attendance.date = ? WHERE enroll.class_id = ? AND enroll.section_id = ? AND enroll.branch_id = ? AND enroll.session_id = ?";
            $attendencelist=DB::select($sql, [$date,$classID,$sectionID,$branchId,get_session_id()]);
        } elseif($request->isMethod('post') && $request->has('save')){
            abort_unless(get_permission('student_attendance','is_add'),403);
            $attendance=$request->input('attendance',[]); $date=$request->input('date');
            foreach($attendance as $value){
                $attStatus=$value['status'] ?? "";
                $array=['student_id'=>$value['student_id'],'status'=>$attStatus,'remark'=>$value['remark'],'date'=>$date,'branch_id'=>$branchId];
                if(empty($value['attendance_id'])) DB::table('student_attendance')->insert($array);
                else DB::table('student_attendance')->where('id',$value['attendance_id'])->update(['status'=>$attStatus,'remark'=>$value['remark']]);
            }
            set_alert('success', translate('information_has_been_updated_successfully'));
            return redirect()->route('attendance.student');
        }
        return view('attendance.student_entries',['title'=>translate('student_attendance'),'branch_id'=>$branchId,'branches'=>$branches,'classes'=>$classes,'attendencelist'=>$attendencelist,'date'=>$date]);
    }
    public function studentSave(Request $request)
    {
        // alternative endpoint for ajax bulk save
        return $this->student($request);
    }
    public function staff(Request $request)
    {
        abort_unless(get_permission('staff_attendance','is_view') || get_permission('employee_attendance','is_add') || get_permission('staff_attendance','is_view'),403);
        $branchId=$this->resolveBranchId($request);
        $branches=is_superadmin_loggedin()?Branch::orderBy('name')->get():collect();
        $roles=DB::table('roles')->whereNotIn('id',[6,7])->get();
        $attendencelist=null; $date=null;
        if($request->isMethod('post') && $request->has('search')){
            $rules=['staff_role'=>['required'],'date'=>['required','date']];
            if(is_superadmin_loggedin()) $rules['branch_id']=['required'];
            $validated=$request->validate($rules);
            if(strtotime($validated['date'])>strtotime(date('Y-m-d'))) return back()->withErrors(['date'=>'Please Enter Correct Date'])->withInput();
            $roleID=$validated['staff_role']; $date=$validated['date'];
            $sql="SELECT staff.*, lc.role, sa.id as atten_id, IFNULL(sa.status, 0) as att_status, sa.remark as att_remark FROM staff LEFT JOIN login_credential as lc ON lc.user_id = staff.id and lc.role != '6' and lc.role != '7' LEFT JOIN staff_attendance as sa ON sa.staff_id = staff.id and sa.date = ? WHERE staff.branch_id = ? AND lc.role = ? AND lc.active = 1 ORDER BY staff.id ASC";
            $attendencelist=DB::select($sql, [$date,$branchId,$roleID]);
        } elseif($request->isMethod('post') && $request->has('save')){
            $attendance=$request->input('attendance',[]); $date=$request->input('date');
            foreach($attendance as $value){
                $attStatus=$value['status'] ?? "";
                $array=['staff_id'=>$value['staff_id'],'status'=>$attStatus,'remark'=>$value['remark'],'date'=>$date,'branch_id'=>$branchId];
                if(empty($value['attendance_id'])) DB::table('staff_attendance')->insert($array);
                else DB::table('staff_attendance')->where('id',$value['attendance_id'])->update(['status'=>$attStatus,'remark'=>$value['remark']]);
            }
            set_alert('success', translate('information_has_been_updated_successfully'));
            return redirect()->route('attendance.staff');
        }
        return view('attendance.employees_entries',['title'=>translate('employee_attendance'),'branch_id'=>$branchId,'branches'=>$branches,'roles'=>$roles,'attendencelist'=>$attendencelist,'date'=>$date]);
    }
}
