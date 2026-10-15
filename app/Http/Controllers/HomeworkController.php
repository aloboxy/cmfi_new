<?php
namespace App\Http\Controllers;
use App\Models\Branch;
use App\Models\Homework;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
class HomeworkController extends Controller
{
    private function resolveBranchId(Request $request): int
    {
        if(is_superadmin_loggedin()){ $bid=$request->input('branch_id'); if($bid) return (int)$bid; return (int)(DB::table('branch')->value('id') ?? 0);}
        return (int)get_loggedin_branch_id();
    }
    public function index(Request $request)
    {
        abort_unless(get_permission('homework','is_view'),403);
        $branchId=$this->resolveBranchId($request);
        $branches=is_superadmin_loggedin()?Branch::orderBy('name')->get():collect();
        $classes=$branchId?DB::table('class')->where('branch_id',$branchId)->get():collect();
        $homeworklist=null;
        if($request->isMethod('post')){
            $classID=$request->input('class_id'); $sectionID=$request->input('section_id'); $subjectID=$request->input('subject_id');
            $homeworklist=DB::table('homework')->leftJoin('subject','subject.id','=','homework.subject_id')->leftJoin('class','class.id','=','homework.class_id')->leftJoin('section','section.id','=','homework.section_id')->leftJoin('staff','staff.id','=','homework.created_by')
                ->select('homework.*','subject.name as subject_name','class.name as class_name','section.name as section_name','staff.name as creator_name')
                ->where('homework.class_id',$classID)->where('homework.section_id',$sectionID)->where('homework.subject_id',$subjectID)->where('homework.branch_id',$branchId)->where('homework.session_id',get_session_id())->orderBy('homework.id','desc')->get();
        }
        return view('homework.index',['title'=>translate('homework'),'branch_id'=>$branchId,'branches'=>$branches,'classes'=>$classes,'homeworklist'=>$homeworklist]);
    }
    public function create()
    {
        abort_unless(get_permission('homework','is_add'),403);
        $branchId=$this->resolveBranchId(request());
        $branches=is_superadmin_loggedin()?Branch::orderBy('name')->get():collect();
        $classes=$branchId?DB::table('class')->where('branch_id',$branchId)->get():collect();
        return view('homework.add',['title'=>translate('homework'),'branch_id'=>$branchId,'branches'=>$branches,'classes'=>$classes]);
    }
    public function store(Request $request)
    {
        abort_unless(get_permission('homework','is_add'),403);
        $rules=['class_id'=>['required','integer'],'section_id'=>['required','integer'],'subject_id'=>['required','integer'],'date_of_homework'=>['required','date'],'date_of_submission'=>['required','date'],'homework'=>['required','string']];
        if(is_superadmin_loggedin()) $rules['branch_id']=['required','integer','exists:branch,id'];
        if($request->has('published_later')) $rules['schedule_date']=['required','date'];
        if($request->hasFile('attachment_file')) $rules['attachment_file']=['file','max:2048','mimes:txt,pdf,doc,xls,docx,xlsx,jpg,jpeg,png,gif,bmp'];
        else if(!$request->has('homework_id')) $rules['attachment_file']=['required','file'];
        $validated=$request->validate($rules);
        $branchId=$this->resolveBranchId($request);
        $status=$request->has('published_later')?1:0;
        $data=['branch_id'=>$branchId,'class_id'=>$validated['class_id'],'section_id'=>$validated['section_id'],'session_id'=>get_session_id(),'subject_id'=>$validated['subject_id'],'date_of_homework'=>date("Y-m-d",strtotime($validated['date_of_homework'])),'date_of_submission'=>date("Y-m-d",strtotime($validated['date_of_submission'])),'description'=>$validated['homework'],'created_by'=>get_loggedin_user_id(),'create_date'=>date("Y-m-d"),'status'=>$status,'sms_notification'=>$request->has('notification_sms')?1:0,'schedule_date'=>$request->has('published_later')?date("Y-m-d",strtotime($request->input('schedule_date'))):null,'document'=>'','evaluated_by'=>0];
        if($request->has('homework_id')){
            $id=$request->input('homework_id');
            $hw=Homework::where('id',$id)->when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id',get_loggedin_branch_id()))->firstOrFail();
            $hw->update($data);
            $insertId=$id;
        } else {
            $hw=Homework::create($data); $insertId=$hw->id;
        }
        if($request->hasFile('attachment_file')){
            $ext=$request->file('attachment_file')->getClientOriginalExtension();
            $orig=$request->file('attachment_file')->getClientOriginalName();
            $dir=public_path('uploads/attachments/homework'); if(!is_dir($dir)) mkdir($dir,0777,true);
            $request->file('attachment_file')->move($dir, $insertId.'.'.$ext);
            Homework::where('id',$insertId)->update(['document'=>$orig]);
        } else {
            $doc=$request->input('old_document','');
            if($doc) Homework::where('id',$insertId)->update(['document'=>$doc]);
        }
        set_alert('success', translate('information_has_been_saved_successfully'));
        if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success','url'=>route('homework.index')]);
        return redirect()->route('homework.index');
    }
    public function edit(int $id)
    {
        abort_unless(get_permission('homework','is_edit'),403);
        $homework=Homework::where('id',$id)->when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id',get_loggedin_branch_id()))->firstOrFail();
        $branchId=$this->resolveBranchId(request());
        $branches=is_superadmin_loggedin()?Branch::orderBy('name')->get():collect();
        $classes=$branchId?DB::table('class')->where('branch_id',$branchId)->get():collect();
        return view('homework.edit',['title'=>translate('homework'),'homework'=>$homework,'branches'=>$branches,'classes'=>$classes]);
    }
    public function update(Request $request,int $id)
    {
        $request->merge(['homework_id'=>$id]);
        return $this->store($request);
    }
    public function destroy(Request $request,int $id)
    {
        abort_unless(get_permission('homework','is_delete'),403);
        $q=Homework::where('id',$id);
        if(!is_superadmin_loggedin()) $q->where('branch_id', get_loggedin_branch_id());
        $hw=$q->first();
        if($hw){
            $ext=pathinfo($hw->document ?? '', PATHINFO_EXTENSION);
            $q->delete();
            $path=public_path('uploads/attachments/homework/'.$id.'.'.$ext);
            if(file_exists($path)) unlink($path);
        }
        if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success']);
        set_alert('success', translate('information_has_been_deleted_successfully'));
        return back();
    }
    public function evaluate(int $id)
    {
        abort_unless(get_permission('homework_evaluate','is_view') || get_permission('evaluation_report','is_view'),403);
        $list=DB::table('homework')->join('enroll as e','e.class_id','=','homework.class_id')
            ->where('e.section_id', DB::raw('homework.section_id'))->where('e.session_id', DB::raw('homework.session_id'))
            ->join('student as s','e.student_id','=','s.id')
            ->leftJoin('homework_evaluation as he', function($j){ $j->on('he.homework_id','=','homework.id')->on('he.student_id','=','e.student_id'); })
            ->leftJoin('subject','subject.id','=','homework.subject_id')->leftJoin('class','class.id','=','homework.class_id')->leftJoin('section','section.id','=','homework.section_id')
            ->where('homework.id',$id)->when(!is_superadmin_loggedin(), fn($q)=>$q->where('homework.branch_id',get_loggedin_branch_id()))->where('homework.session_id',get_session_id())
            ->select('homework.*','s.first_name','s.last_name','s.register_no','e.student_id','e.roll','subject.name as subject_name','class.name as class_name','section.name as section_name','he.id as ev_id','he.status as ev_status','he.remark as ev_remarks','he.rank')->get();
        return view('homework.evaluate_list',['title'=>translate('homework'),'homeworklist'=>$list,'homework_id'=>$id]);
    }
    public function evaluateSave(Request $request)
    {
        abort_unless(get_permission('homework_evaluate','is_add'),403);
        $request->validate(['homework_id'=>['required','integer'],'date'=>['required','date']]);
        $evaluate=$request->input('evaluate',[]); $homeworkID=$request->input('homework_id'); $date=date("Y-m-d",strtotime($request->input('date')));
        foreach($evaluate as $value){
            $attStatus=$value['status'] ?? "";
            $array=['homework_id'=>$homeworkID,'student_id'=>$value['student_id'],'status'=>$attStatus,'rank'=>$value['rank']??0,'remark'=>$value['remark']??'','date'=>$date];
            if(empty($value['evaluation_id'])) DB::table('homework_evaluation')->insert($array);
            else DB::table('homework_evaluation')->where('id',$value['evaluation_id'])->update(['rank'=>$value['rank']??0,'status'=>$attStatus,'remark'=>$value['remark']??'','date'=>$date]);
        }
        DB::table('homework')->where('id',$homeworkID)->update(['evaluation_date'=>$date,'evaluated_by'=>get_loggedin_user_id()]);
        set_alert('success', translate('information_has_been_saved_successfully'));
        if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success','url'=>route('homework.index')]);
        return redirect()->route('homework.index');
    }
    public function report(Request $request)
    {
        abort_unless(get_permission('evaluation_report','is_view'),403);
        $branchId=$this->resolveBranchId($request);
        $branches=is_superadmin_loggedin()?Branch::orderBy('name')->get():collect();
        $classes=$branchId?DB::table('class')->where('branch_id',$branchId)->get():collect();
        $homeworklist=null;
        if($request->isMethod('post')){
            $classID=$request->input('class_id'); $sectionID=$request->input('section_id'); $subjectID=$request->input('subject_id');
            $homeworklist=DB::table('homework')->leftJoin('subject','subject.id','=','homework.subject_id')->leftJoin('class','class.id','=','homework.class_id')->leftJoin('section','section.id','=','homework.section_id')->leftJoin('staff','staff.id','=','homework.created_by')
                ->select('homework.*','subject.name as subject_name','class.name as class_name','section.name as section_name','staff.name as creator_name')
                ->where('homework.class_id',$classID)->where('homework.section_id',$sectionID)->where('homework.subject_id',$subjectID)->where('homework.branch_id',$branchId)->where('homework.session_id',get_session_id())->orderBy('homework.id','desc')->get();
        }
        return view('homework.report',['title'=>translate('homework'),'branch_id'=>$branchId,'branches'=>$branches,'classes'=>$classes,'homeworklist'=>$homeworklist]);
    }
}
