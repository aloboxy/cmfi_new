<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Exam;
use App\Models\ExamHall;
use App\Models\ExamMarkDistribution;
use App\Models\ExamTerm;
use App\Models\Grade;
use App\Models\ReportCardTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    private function resolveBranchId(Request $request): ?int
    {
        if (is_superadmin_loggedin()) {
            $bid = $request->input('branch_id');
            if ($bid) return (int)$bid;
            $fallback = $request->input('branch_id', request()->query('branch_id'));
            if (!empty($fallback)) return (int)$fallback;
            return (int) (DB::table('branch')->value('id') ?? 0);
        }
        return (int) get_loggedin_branch_id();
    }

    // ---- Exam list/setup ----
    public function index(Request $request)
    {
        abort_unless(get_permission('exam','is_view'),403,translate('access_denied'));
        $branchList = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();
        $exams = DB::table('exam as e')->leftJoin('branch as b','b.id','=','e.branch_id')
            ->select('e.*','b.name as branch_name')
            ->when(!is_superadmin_loggedin(), fn($q)=>$q->where('e.branch_id', get_loggedin_branch_id()))
            ->where('e.session_id', get_session_id())->orderBy('e.id','asc')->get();
        $terms = DB::table('exam_term')->when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id', get_loggedin_branch_id()))->where('session_id', get_session_id())->get();
        $distributions = DB::table('exam_mark_distribution')->when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id', get_loggedin_branch_id()))->get();
        return view('exam.index', [
            'title'=>translate('exam_list'),
            'examlist'=>$exams,
            'branch_id'=>$this->resolveBranchId($request),
            'branches'=>$branchList,
            'terms'=>$terms,
            'distributions'=>$distributions,
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(get_permission('exam', is_superadmin_loggedin() ? 'is_view' : 'is_add') || get_permission('exam','is_add'),403);
        $rules = [
            'name'=>['required','string','max:255'],
            'term_id'=>['required','integer','exists:exam_term,id'],
            'type_id'=>['required','integer','in:1,2,3'],
            'mark_distribution'=>['nullable','array'],
            'mark_distribution.*'=>['integer','exists:exam_mark_distribution,id'],
            'remark'=>['nullable','string'],
        ];
        if(is_superadmin_loggedin()) $rules['branch_id']=['required','integer','exists:branch,id'];
        $validated = $request->validate($rules);
        $branchId = $this->resolveBranchId($request);
        $dist = !empty($validated['mark_distribution']) ? json_encode(array_values(array_map('intval', $validated['mark_distribution']))) : null;
        $data = [
            'name'=>$validated['name'],
            'term_id'=>$validated['term_id'],
            'type_id'=>$validated['type_id'],
            'mark_distribution'=>$dist,
            'remark'=>$validated['remark'] ?? '',
            'branch_id'=>$branchId,
            'session_id'=>get_session_id(),
        ];
        Exam::create($data);
        set_alert('success', translate('information_has_been_saved_successfully'));
        if($request->expectsJson() || $request->ajax()) return response()->json(['status'=>'success','url'=>route('exam.index')]);
        return redirect()->route('exam.index');
    }

    public function edit(int $id)
    {
        abort_unless(get_permission('exam','is_edit'),403);
        $exam = Exam::where('id',$id)->when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id',get_loggedin_branch_id()))->firstOrFail();
        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();
        $terms = DB::table('exam_term')->when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id', get_loggedin_branch_id()))->where('session_id', get_session_id())->get();
        $distributions = DB::table('exam_mark_distribution')->when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id', get_loggedin_branch_id()))->get();
        return view('exam.edit', ['title'=>translate('exam_list'),'exam'=>$exam,'branches'=>$branches,'terms'=>$terms,'distributions'=>$distributions]);
    }

    public function update(Request $request,int $id)
    {
        abort_unless(get_permission('exam','is_edit'),403);
        $rules = [
            'name'=>['required','string','max:255'],
            'term_id'=>['required','integer','exists:exam_term,id'],
            'type_id'=>['required','integer','in:1,2,3'],
            'mark_distribution'=>['nullable','array'],
            'mark_distribution.*'=>['integer','exists:exam_mark_distribution,id'],
            'remark'=>['nullable','string'],
        ];
        if(is_superadmin_loggedin()) $rules['branch_id']=['required','integer','exists:branch,id'];
        $validated = $request->validate($rules);
        $branchId = $this->resolveBranchId($request);
        $exam = Exam::where('id',$id)->when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id',get_loggedin_branch_id()))->firstOrFail();
        $dist = !empty($validated['mark_distribution']) ? json_encode(array_values(array_map('intval', $validated['mark_distribution']))) : null;
        $exam->update([
            'name'=>$validated['name'],
            'term_id'=>$validated['term_id'],
            'type_id'=>$validated['type_id'],
            'mark_distribution'=>$dist,
            'remark'=>$validated['remark'] ?? '',
            'branch_id'=>$branchId,
        ]);
        set_alert('success', translate('information_has_been_updated_successfully'));
        if($request->expectsJson() || $request->ajax()) return response()->json(['status'=>'success','url'=>route('exam.index')]);
        return redirect()->route('exam.index');
    }

    public function destroy(Request $request,int $id)
    {
        abort_unless(get_permission('exam','is_delete'),403);
        $q = Exam::where('id',$id);
        if(!is_superadmin_loggedin()) $q->where('branch_id', get_loggedin_branch_id());
        $q->delete();
        if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success']);
        set_alert('success', translate('information_has_been_deleted_successfully'));
        return back();
    }

    // ---- Term ----
    public function term(Request $request)
    {
        abort_unless(get_permission('exam_term','is_view'),403);
        $branchList = is_superadmin_loggedin() ? Branch::orderBy('name')->get():collect();
        $termlist = DB::table('exam_term as t')->leftJoin('branch as b','b.id','=','t.branch_id')
            ->select('t.*','b.name as branch_name')
            ->when(!is_superadmin_loggedin(), fn($q)=>$q->where('t.branch_id', get_loggedin_branch_id()))
            ->orderBy('t.id','asc')->get();
        return view('exam.term', ['title'=>translate('exam_term'),'termlist'=>$termlist,'branches'=>$branchList]);
    }

    public function termStore(Request $request)
    {
        abort_unless(get_permission('exam_term','is_add'),403);
        $rules=['term_name'=>['required','string','max:255']];
        if(is_superadmin_loggedin()) $rules['branch_id']=['required','integer','exists:branch,id'];
        $validated=$request->validate($rules);
        $branchId=$this->resolveBranchId($request);
        $exists=ExamTerm::where('name',$validated['term_name'])->where('branch_id',$branchId)->exists();
        if($exists) return back()->withErrors(['term_name'=>translate('already_taken')])->withInput();
        ExamTerm::create(['name'=>$validated['term_name'],'branch_id'=>$branchId,'session_id'=>get_session_id()]);
        set_alert('success', translate('information_has_been_saved_successfully'));
        return redirect()->route('exam.term');
    }

    public function termEdit(Request $request)
    {
        abort_unless(get_permission('exam_term','is_edit'),403);
        $rules=['term_id'=>['required','integer','exists:exam_term,id'],'term_name'=>['required','string','max:255']];
        if(is_superadmin_loggedin()) $rules['branch_id']=['required','integer','exists:branch,id'];
        $validated=$request->validate($rules);
        $branchId=$this->resolveBranchId($request);
        $exists=ExamTerm::where('name',$validated['term_name'])->where('branch_id',$branchId)->where('id','!=',$validated['term_id'])->exists();
        if($exists) {
            if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'fail','error'=>['term_name'=>translate('already_taken')]]);
            return back()->withErrors(['term_name'=>translate('already_taken')]);
        }
        $term=ExamTerm::where('id',$validated['term_id'])->when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id',get_loggedin_branch_id()))->firstOrFail();
        $term->update(['name'=>$validated['term_name'],'branch_id'=>$branchId]);
        set_alert('success', translate('information_has_been_updated_successfully'));
        if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success','url'=>route('exam.term')]);
        return redirect()->route('exam.term');
    }

    public function termDelete(Request $request,int $id)
    {
        abort_unless(get_permission('exam_term','is_delete'),403);
        $q=ExamTerm::where('id',$id);
        if(!is_superadmin_loggedin()) $q->where('branch_id', get_loggedin_branch_id());
        $q->delete();
        if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success']);
        set_alert('success', translate('information_has_been_deleted_successfully'));
        return back();
    }

    // ---- Mark distribution ----
    public function markDistribution(Request $request)
    {
        abort_unless(get_permission('mark_distribution','is_view'),403);
        $branches=is_superadmin_loggedin()?Branch::orderBy('name')->get():collect();
        $list=DB::table('exam_mark_distribution as t')->leftJoin('branch as b','b.id','=','t.branch_id')->select('t.*','b.name as branch_name')
            ->when(!is_superadmin_loggedin(), fn($q)=>$q->where('t.branch_id', get_loggedin_branch_id()))->orderBy('t.id','asc')->get();
        return view('exam.mark_distribution', ['title'=>translate('mark_distribution'),'termlist'=>$list,'branches'=>$branches]);
    }
    public function markDistributionStore(Request $request)
    {
        abort_unless(get_permission('mark_distribution','is_add'),403);
        $rules=['name'=>['required','string','max:255']];
        if(is_superadmin_loggedin()) $rules['branch_id']=['required','integer','exists:branch,id'];
        $validated=$request->validate($rules);
        $branchId=$this->resolveBranchId($request);
        ExamMarkDistribution::create(['name'=>$validated['name'],'branch_id'=>$branchId]);
        set_alert('success', translate('information_has_been_saved_successfully'));
        return redirect()->route('exam.mark_distribution');
    }
    public function markDistributionEdit(Request $request)
    {
        abort_unless(get_permission('mark_distribution','is_edit'),403);
        $rules=['distribution_id'=>['required','integer','exists:exam_mark_distribution,id'],'name'=>['required','string','max:255']];
        if(is_superadmin_loggedin()) $rules['branch_id']=['required','integer','exists:branch,id'];
        $validated=$request->validate($rules);
        $branchId=$this->resolveBranchId($request);
        $dist=ExamMarkDistribution::where('id',$validated['distribution_id'])->when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id',get_loggedin_branch_id()))->firstOrFail();
        $dist->update(['name'=>$validated['name'],'branch_id'=>$branchId]);
        set_alert('success', translate('information_has_been_updated_successfully'));
        if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success','url'=>route('exam.mark_distribution')]);
        return redirect()->route('exam.mark_distribution');
    }
    public function markDistributionDelete(Request $request,int $id)
    {
        abort_unless(get_permission('mark_distribution','is_delete'),403);
        $q=ExamMarkDistribution::where('id',$id);
        if(!is_superadmin_loggedin()) $q->where('branch_id', get_loggedin_branch_id());
        $q->delete();
        if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success']);
        set_alert('success', translate('information_has_been_deleted_successfully'));
        return back();
    }

    // ---- Hall ----
    public function hall(Request $request)
    {
        abort_unless(get_permission('exam_hall','is_view'),403);
        $branches=is_superadmin_loggedin()?Branch::orderBy('name')->get():collect();
        $list=DB::table('exam_hall as t')->leftJoin('branch as b','b.id','=','t.branch_id')->select('t.*','b.name as branch_name')
            ->when(!is_superadmin_loggedin(), fn($q)=>$q->where('t.branch_id', get_loggedin_branch_id()))->orderBy('t.id','asc')->get();
        return view('exam.hall', ['title'=>translate('exam_hall'),'halllist'=>$list,'branches'=>$branches]);
    }
    public function hallStore(Request $request)
    {
        abort_unless(get_permission('exam_hall','is_add'),403);
        $rules=['hall_no'=>['required','string','max:255'],'no_of_seats'=>['required','numeric']];
        if(is_superadmin_loggedin()) $rules['branch_id']=['required','integer','exists:branch,id'];
        $validated=$request->validate($rules);
        $branchId=$this->resolveBranchId($request);
        $exists=ExamHall::where('hall_no',$validated['hall_no'])->where('branch_id',$branchId)->exists();
        if($exists) return back()->withErrors(['hall_no'=>translate('already_taken')])->withInput();
        ExamHall::create(['hall_no'=>$validated['hall_no'],'seats'=>$validated['no_of_seats'],'branch_id'=>$branchId]);
        set_alert('success', translate('information_has_been_saved_successfully'));
        return redirect()->route('exam.hall');
    }
    public function hallEdit(Request $request)
    {
        abort_unless(get_permission('exam_hall','is_edit'),403);
        $rules=['hall_id'=>['required','integer','exists:exam_hall,id'],'hall_no'=>['required','string','max:255'],'no_of_seats'=>['required','numeric']];
        if(is_superadmin_loggedin()) $rules['branch_id']=['required','integer','exists:branch,id'];
        $validated=$request->validate($rules);
        $branchId=$this->resolveBranchId($request);
        $exists=ExamHall::where('hall_no',$validated['hall_no'])->where('branch_id',$branchId)->where('id','!=',$validated['hall_id'])->exists();
        if($exists) {
            if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'fail','error'=>['hall_no'=>translate('already_taken')]]);
            return back()->withErrors(['hall_no'=>translate('already_taken')]);
        }
        $hall=ExamHall::where('id',$validated['hall_id'])->when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id',get_loggedin_branch_id()))->firstOrFail();
        $hall->update(['hall_no'=>$validated['hall_no'],'seats'=>$validated['no_of_seats'],'branch_id'=>$branchId]);
        set_alert('success', translate('information_has_been_updated_successfully'));
        if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success','url'=>route('exam.hall')]);
        return redirect()->route('exam.hall');
    }
    public function hallDelete(Request $request,int $id)
    {
        abort_unless(get_permission('exam_hall','is_delete'),403);
        $q=ExamHall::where('id',$id);
        if(!is_superadmin_loggedin()) $q->where('branch_id', get_loggedin_branch_id());
        $q->delete();
        if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success']);
        set_alert('success', translate('information_has_been_deleted_successfully'));
        return back();
    }

    // ---- Marks ----
    public function marks(Request $request)
    {
        abort_unless(get_permission('exam_mark','is_view'),403);
        $branchId=$this->resolveBranchId($request);
        $branches=is_superadmin_loggedin()?Branch::orderBy('name')->get():collect();
        $classes=$branchId? DB::table('class')->where('branch_id',$branchId)->get() : collect();
        $exams=$branchId? DB::table('exam')->where('branch_id',$branchId)->where('session_id',get_session_id())->get():collect();
        $timetableDetail=null;
        $students=null;
        $classID=$request->input('class_id');
        $sectionID=$request->input('section_id');
        $subjectID=$request->input('subject_id');
        $examID=$request->input('exam_id');
        $exam=null;
        $distributionItems=[];
        $hasDistribution=false;
        $subjectInfo=null;

        if($request->isMethod('post') && $request->has('search')){
            $request->validate(['class_id'=>['required'],'section_id'=>['required'],'subject_id'=>['required'],'exam_id'=>['required']]);
            $exam = DB::table('exam')->where('id', $examID)->first();
            $subjectInfo = DB::table('subject')->where('id', $subjectID)->first();
            $timetableDetail=DB::table('timetable_exam')->where('class_id',$classID)->where('section_id',$sectionID)->where('exam_id',$examID)->where('subject_id',$subjectID)->where('session_id',get_session_id())->first();
            
            // Check mark distributions
            if ($timetableDetail && !empty($timetableDetail->mark_distribution)) {
                $td = json_decode($timetableDetail->mark_distribution, true);
                if (is_array($td) && !empty($td)) {
                    $hasDistribution = true;
                    $distributionItems = $td;
                }
            } elseif ($exam && !empty($exam->mark_distribution)) {
                $distIds = json_decode($exam->mark_distribution, true) ?: [];
                if (!empty($distIds)) {
                    $dists = DB::table('exam_mark_distribution')->whereIn('id', $distIds)->get();
                    if ($dists->isNotEmpty()) {
                        $hasDistribution = true;
                        $share = round(100 / count($dists), 0);
                        foreach ($dists as $d) {
                            $distributionItems[$d->id] = [
                                'name' => $d->name,
                                'full_mark' => $share,
                                'pass_mark' => round($share * 0.5, 0),
                            ];
                        }
                    }
                }
            }

            $students=DB::table('enroll as en')
                ->join('student as st','st.id','=','en.student_id')
                ->join('login_credential as l', function($j){ $j->on('l.user_id','=','st.id')->where('l.role',7); })
                ->leftJoin('mark as m', function($j) use($examID,$subjectID){ $j->on('m.student_id','=','en.student_id')->where('m.class_id','=', DB::raw('en.class_id'))->where('m.section_id','=', DB::raw('en.section_id'))->where('m.exam_id','=', DB::raw((int)$examID))->where('m.subject_id','=', DB::raw((int)$subjectID)); })
                ->leftJoin('subject','subject.id','=','m.subject_id')
                ->where('l.active',1)->where('en.class_id',$classID)->where('en.section_id',$sectionID)->where('en.branch_id',$branchId)->where('en.session_id',get_session_id())
                ->select('en.*','st.first_name','st.last_name','st.register_no','st.category_id','st.photo','m.mark as get_mark','m.absent as get_abs','subject.name as subject_name')
                ->orderBy('en.roll','asc')->get();
        }

        return view('exam.marks_register', [
            'title'=>translate('mark_entries'),
            'branch_id'=>$branchId,
            'branches'=>$branches,
            'classes'=>$classes,
            'exams'=>$exams,
            'timetable_detail'=>$timetableDetail,
            'students'=>$students,
            'class_id'=>$classID,
            'section_id'=>$sectionID,
            'subject_id'=>$subjectID,
            'exam_id'=>$examID,
            'exam'=>$exam,
            'subjectInfo'=>$subjectInfo,
            'hasDistribution'=>$hasDistribution,
            'distributionItems'=>$distributionItems,
        ]);
    }

    public function markSave(Request $request)
    {
        abort_unless(get_permission('exam_mark','is_add'),403);
        $inputMarks=$request->input('mark',[]);
        foreach($inputMarks as $k=>$v){
            if(!isset($v['absent'])){
                foreach($v['assessment'] ?? [] as $i=>$row){
                    $request->validate(["mark.$k.assessment.$i"=>['nullable','numeric']]);
                }
            }
        }
        $branchId=$this->resolveBranchId($request);
        $classID=$request->input('class_id');
        $sectionID=$request->input('section_id');
        $subjectID=$request->input('subject_id');
        $examID=$request->input('exam_id');
        foreach($inputMarks as $key=>$value){
            $assMark=[];
            foreach($value['assessment'] ?? [] as $i=>$row) {
                if ($row !== '' && $row !== null) $assMark[$i] = (float)$row;
            }
            $arrayMarks=['student_id'=>$value['student_id'],'exam_id'=>$examID,'class_id'=>$classID,'section_id'=>$sectionID,'subject_id'=>$subjectID,'branch_id'=>$branchId,'session_id'=>get_session_id()];
            $inputMark=(isset($value['absent']) ? null : json_encode($assMark));
            $absent=(isset($value['absent'])?'on':'');
            $existing=DB::table('mark')->where($arrayMarks)->first();
            if($existing){
                DB::table('mark')->where('id',$existing->id)->update(['mark'=>$inputMark,'absent'=>$absent]);
            } else {
                $arrayMarks['mark']=$inputMark;
                $arrayMarks['absent']=$absent;
                DB::table('mark')->insert($arrayMarks);
            }
        }
        if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success','message'=>translate('information_has_been_saved_successfully')]);
        set_alert('success', translate('information_has_been_saved_successfully'));
        return back();
    }

    // ---- Master GradeSheet (Tabulation Sheet) ----
    public function tabulationSheet(Request $request)
    {
        abort_unless(get_permission('exam_mark', 'is_view'), 403, translate('access_denied'));
        $branchId = $this->resolveBranchId($request);
        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();
        $sessions = DB::table('schoolyear')->orderBy('id', 'desc')->get();
        $sessionId = (int) ($request->input('session_id') ?: get_session_id());
        $classes = $branchId ? DB::table('class')->where('branch_id', $branchId)->get() : collect();
        $exams = $branchId ? DB::table('exam')->where('branch_id', $branchId)->where('session_id', $sessionId)->get() : collect();

        $classID = $request->input('class_id');
        $sectionID = $request->input('section_id');
        $examID = $request->input('exam_id');

        $exam = null;
        $classObj = null;
        $sectionObj = null;
        $subjects = collect();
        $marksMatrix = [];
        $classStats = [
            'total_students' => 0,
            'appeared_students' => 0,
            'passed_students' => 0,
            'failed_students' => 0,
            'pass_rate' => 0,
            'highest_score' => 0,
            'average_score' => 0,
        ];

        if ($classID && $sectionID && $examID) {
            $exam = DB::table('exam')->where('id', $examID)->first();
            $classObj = DB::table('class')->where('id', $classID)->first();
            $sectionObj = DB::table('section')->where('id', $sectionID)->first();

            // 1. Get subjects for this class and section
            $assignedSubjectIds = DB::table('subject_assign')
                ->where('class_id', $classID)
                ->where('section_id', $sectionID)
                ->where('session_id', $sessionId)
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->pluck('subject_id')
                ->toArray();

            if (empty($assignedSubjectIds)) {
                $subjects = DB::table('subject')
                    ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                    ->get();
            } else {
                $subjects = DB::table('subject')->whereIn('id', $assignedSubjectIds)->get();
            }

            // 2. Timetable exam schedule details (for subject full marks)
            $timetableMap = DB::table('timetable_exam')
                ->where('class_id', $classID)
                ->where('section_id', $sectionID)
                ->where('exam_id', $examID)
                ->where('session_id', $sessionId)
                ->get()
                ->keyBy('subject_id');

            // 3. Get all active enrolled students
            $students = DB::table('enroll as en')
                ->join('student as st', 'st.id', '=', 'en.student_id')
                ->join('login_credential as l', function($j) {
                    $j->on('l.user_id', '=', 'st.id')->where('l.role', ROLE_STUDENT);
                })
                ->where('l.active', 1)
                ->where('en.class_id', $classID)
                ->where('en.section_id', $sectionID)
                ->where('en.session_id', $sessionId)
                ->when($branchId, fn($q) => $q->where('en.branch_id', $branchId))
                ->select('en.*', 'st.first_name', 'st.last_name', 'st.register_no', 'st.photo', 'st.gender', 'st.email')
                ->orderBy('en.roll', 'asc')
                ->get();

            // 4. Get all marks
            $marksRows = DB::table('mark')
                ->where('exam_id', $examID)
                ->where('class_id', $classID)
                ->where('section_id', $sectionID)
                ->where('session_id', $sessionId)
                ->get();

            $marksLookup = [];
            foreach ($marksRows as $m) {
                $marksLookup[$m->student_id][$m->subject_id] = $m;
            }

            // 5. Build full gradesheet matrix
            $studentRows = [];
            $totalMarksAllStudents = 0;
            $highestScore = 0;

            foreach ($students as $st) {
                $stId = $st->student_id;
                $subjectResults = [];
                $studentTotalObtained = 0;
                $studentTotalMax = 0;
                $studentPassedAll = true;
                $hasAppeared = false;
                $totalGpaPoints = 0;
                $subjectsCount = 0;

                foreach ($subjects as $sub) {
                    $subId = $sub->id;
                    $markRecord = $marksLookup[$stId][$subId] ?? null;
                    
                    // Determine subject full mark
                    $subFullMark = 100.0;
                    if (isset($timetableMap[$subId]) && !empty($timetableMap[$subId]->mark_distribution)) {
                        $tdist = json_decode($timetableMap[$subId]->mark_distribution, true);
                        if (is_array($tdist)) {
                            $calcFull = 0;
                            foreach ($tdist as $td) {
                                $calcFull += (float) ($td['full_mark'] ?? 0);
                            }
                            if ($calcFull > 0) $subFullMark = $calcFull;
                        }
                    }

                    $obtainedMark = null;
                    $isAbsent = false;

                    if ($markRecord) {
                        $hasAppeared = true;
                        if ($markRecord->absent === 'on' || $markRecord->absent == 1) {
                            $isAbsent = true;
                            $obtainedMark = null;
                        } else {
                            $decoded = json_decode($markRecord->mark ?? '', true);
                            if (is_array($decoded)) {
                                $obtainedMark = array_sum(array_map('floatval', $decoded));
                            } elseif (is_numeric($markRecord->mark)) {
                                $obtainedMark = (float) $markRecord->mark;
                            }
                        }
                    }

                    if ($obtainedMark !== null && !$isAbsent) {
                        $studentTotalObtained += $obtainedMark;
                        $studentTotalMax += $subFullMark;
                    } else {
                        $studentTotalMax += $subFullMark;
                    }

                    $gradeInfo = get_grade_info($obtainedMark, $subFullMark, $branchId, $isAbsent);
                    if (!$gradeInfo['is_pass']) {
                        $studentPassedAll = false;
                    }

                    $totalGpaPoints += $gradeInfo['grade_point'];
                    $subjectsCount++;

                    $subjectResults[$subId] = [
                        'subject_id' => $subId,
                        'subject_name' => $sub->name,
                        'subject_code' => $sub->subject_code ?? '',
                        'obtained_mark' => $obtainedMark,
                        'full_mark' => $subFullMark,
                        'is_absent' => $isAbsent,
                        'grade_name' => $gradeInfo['name'],
                        'grade_point' => $gradeInfo['grade_point'],
                        'is_pass' => $gradeInfo['is_pass'],
                        'color' => $gradeInfo['color'], // 'blue' or 'red'
                        'remark' => $gradeInfo['remark'],
                    ];
                }

                $studentPercentage = $studentTotalMax > 0 ? round(($studentTotalObtained / $studentTotalMax) * 100, 2) : 0;
                $studentGpa = $subjectsCount > 0 ? round($totalGpaPoints / $subjectsCount, 2) : 0;
                $overallGrade = get_grade_info($studentTotalObtained, $studentTotalMax, $branchId, false);
                $finalStatus = ($studentPassedAll && $hasAppeared && $studentPercentage >= 50) ? 'PASSED' : 'FAILED';
                $finalColor = ($finalStatus === 'PASSED') ? 'blue' : 'red';

                if ($studentTotalObtained > $highestScore) {
                    $highestScore = $studentTotalObtained;
                }
                $totalMarksAllStudents += $studentTotalObtained;

                $studentRows[] = [
                    'student' => $st,
                    'subject_results' => $subjectResults,
                    'total_obtained' => $studentTotalObtained,
                    'total_max' => $studentTotalMax,
                    'percentage' => $studentPercentage,
                    'gpa' => $studentGpa,
                    'overall_grade' => $overallGrade['name'],
                    'final_status' => $finalStatus,
                    'final_color' => $finalColor,
                    'has_appeared' => $hasAppeared,
                    'is_pass' => ($finalStatus === 'PASSED'),
                ];
            }

            // Calculate ranks (descending by total_obtained)
            usort($studentRows, fn($a, $b) => $b['total_obtained'] <=> $a['total_obtained']);
            $currentRank = 1;
            foreach ($studentRows as $idx => &$row) {
                if ($idx > 0 && $row['total_obtained'] < $studentRows[$idx - 1]['total_obtained']) {
                    $currentRank = $idx + 1;
                }
                $row['rank'] = $currentRank;
            }
            unset($row);

            // Re-sort by student Roll number for standard sheet display
            usort($studentRows, fn($a, $b) => (int)($a['student']->roll ?? 0) <=> (int)($b['student']->roll ?? 0));

            // Stats calculation
            $totalCount = count($studentRows);
            $appearedCount = count(array_filter($studentRows, fn($r) => $r['has_appeared']));
            $passedCount = count(array_filter($studentRows, fn($r) => $r['is_pass']));
            $failedCount = $totalCount - $passedCount;
            $passRate = $appearedCount > 0 ? round(($passedCount / $appearedCount) * 100, 1) : 0;
            $avgScore = $appearedCount > 0 ? round($totalMarksAllStudents / max($appearedCount, 1), 2) : 0;

            $classStats = [
                'total_students' => $totalCount,
                'appeared_students' => $appearedCount,
                'passed_students' => $passedCount,
                'failed_students' => $failedCount,
                'pass_rate' => $passRate,
                'highest_score' => $highestScore,
                'average_score' => $avgScore,
            ];

            $marksMatrix = $studentRows;
        }

        return view('exam.tabulation', [
            'title' => translate('master_gradesheet'),
            'branches' => $branches,
            'sessions' => $sessions,
            'classes' => $classes,
            'exams' => $exams,
            'branch_id' => $branchId,
            'session_id' => $sessionId,
            'class_id' => $classID,
            'section_id' => $sectionID,
            'exam_id' => $examID,
            'exam' => $exam,
            'classObj' => $classObj,
            'sectionObj' => $sectionObj,
            'subjects' => $subjects,
            'marksMatrix' => $marksMatrix,
            'classStats' => $classStats,
        ]);
    }

    public function tabulationExport(Request $request)
    {
        abort_unless(get_permission('exam_mark', 'is_view'), 403);
        $branchId = $this->resolveBranchId($request);
        $sessionId = (int) ($request->input('session_id') ?: get_session_id());
        $classID = $request->input('class_id');
        $sectionID = $request->input('section_id');
        $examID = $request->input('exam_id');

        $exam = DB::table('exam')->where('id', $examID)->first();
        $classObj = DB::table('class')->where('id', $classID)->first();
        $sectionObj = DB::table('section')->where('id', $sectionID)->first();

        $assignedSubjectIds = DB::table('subject_assign')
            ->where('class_id', $classID)
            ->where('section_id', $sectionID)
            ->where('session_id', $sessionId)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->pluck('subject_id')
            ->toArray();

        $subjects = empty($assignedSubjectIds)
            ? DB::table('subject')->when($branchId, fn($q) => $q->where('branch_id', $branchId))->get()
            : DB::table('subject')->whereIn('id', $assignedSubjectIds)->get();

        $students = DB::table('enroll as en')
            ->join('student as st', 'st.id', '=', 'en.student_id')
            ->join('login_credential as l', function($j) {
                $j->on('l.user_id', '=', 'st.id')->where('l.role', ROLE_STUDENT);
            })
            ->where('l.active', 1)
            ->where('en.class_id', $classID)
            ->where('en.section_id', $sectionID)
            ->where('en.session_id', $sessionId)
            ->when($branchId, fn($q) => $q->where('en.branch_id', $branchId))
            ->select('en.*', 'st.first_name', 'st.last_name', 'st.register_no')
            ->orderBy('en.roll', 'asc')
            ->get();

        $marksRows = DB::table('mark')
            ->where('exam_id', $examID)
            ->where('class_id', $classID)
            ->where('section_id', $sectionID)
            ->where('session_id', $sessionId)
            ->get();

        $marksLookup = [];
        foreach ($marksRows as $m) {
            $marksLookup[$m->student_id][$m->subject_id] = $m;
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="master_gradesheet_'.($exam->name ?? 'exam').'.csv"',
        ];

        $callback = function() use ($students, $subjects, $marksLookup, $branchId) {
            $file = fopen('php://output', 'w');
            $headerRow = ['Roll', 'Reg No', 'Student Name'];
            foreach ($subjects as $s) {
                $headerRow[] = $s->name . ' (Marks)';
                $headerRow[] = $s->name . ' (Grade)';
            }
            $headerRow[] = 'Total Obtained';
            $headerRow[] = 'Percentage (%)';
            $headerRow[] = 'GPA';
            $headerRow[] = 'Grade';
            $headerRow[] = 'Status';
            fputcsv($file, $headerRow);

            foreach ($students as $st) {
                $row = [$st->roll, $st->register_no, $st->first_name . ' ' . $st->last_name];
                $totalObtained = 0;
                $totalMax = count($subjects) * 100;
                $allPass = true;

                foreach ($subjects as $s) {
                    $m = $marksLookup[$st->student_id][$s->id] ?? null;
                    $obt = null;
                    $abs = ($m && ($m->absent === 'on' || $m->absent == 1));
                    if ($m && !$abs) {
                        $dec = json_decode($m->mark ?? '', true);
                        $obt = is_array($dec) ? array_sum(array_map('floatval', $dec)) : (float)$m->mark;
                        $totalObtained += $obt;
                    }
                    $gInfo = get_grade_info($obt, 100, $branchId, $abs);
                    if (!$gInfo['is_pass']) $allPass = false;
                    $row[] = $abs ? 'ABS' : ($obt !== null ? $obt : '-');
                    $row[] = $gInfo['name'];
                }

                $pct = $totalMax > 0 ? round(($totalObtained / $totalMax) * 100, 2) : 0;
                $overall = get_grade_info($totalObtained, $totalMax, $branchId, false);
                $status = ($allPass && $pct >= 50) ? 'PASSED' : 'FAILED';

                $row[] = $totalObtained;
                $row[] = $pct . '%';
                $row[] = $overall['grade_point'];
                $row[] = $overall['name'];
                $row[] = $status;
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ---- Grade ----
    public function grade(Request $request)
    {
        abort_unless(get_permission('exam_grade','is_view'),403);
        $branches=is_superadmin_loggedin()?Branch::orderBy('name')->get():collect();
        $grades=DB::table('grade as t')->leftJoin('branch as b','b.id','=','t.branch_id')->select('t.*','b.name as branch_name')
            ->when(!is_superadmin_loggedin(), fn($q)=>$q->where('t.branch_id', get_loggedin_branch_id()))->orderBy('t.id','asc')->get();
        return view('exam.grade', ['title'=>translate('grades_range'),'grades'=>$grades,'branches'=>$branches]);
    }
    public function gradeStore(Request $request)
    {
        abort_unless(get_permission('exam_grade','is_view')||get_permission('exam_grade','is_add'),403);
        $rules=['name'=>['required','string'],'grade_point'=>['required','numeric'],'lower_mark'=>['required','numeric'],'upper_mark'=>['required','numeric'],'remark'=>['nullable','string']];
        if(is_superadmin_loggedin()) $rules['branch_id']=['required','integer','exists:branch,id'];
        $validated=$request->validate($rules);
        $branchId=$this->resolveBranchId($request);
        Grade::create(['branch_id'=>$branchId,'name'=>$validated['name'],'grade_point'=>$validated['grade_point'],'lower_mark'=>$validated['lower_mark'],'upper_mark'=>$validated['upper_mark'],'remark'=>$validated['remark'] ?? '']);
        set_alert('success', translate('information_has_been_saved_successfully'));
        if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success','url'=>route('exam.grade')]);
        return redirect()->route('exam.grade');
    }
    public function gradeUpdate(Request $request,int $id)
    {
        abort_unless(get_permission('exam_grade','is_edit'),403);
        $rules=['name'=>['required','string'],'grade_point'=>['required','numeric'],'lower_mark'=>['required','numeric'],'upper_mark'=>['required','numeric'],'remark'=>['nullable','string']];
        if(is_superadmin_loggedin()) $rules['branch_id']=['required','integer','exists:branch,id'];
        $validated=$request->validate($rules);
        $branchId=$this->resolveBranchId($request);
        $grade=Grade::where('id',$id)->when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id',get_loggedin_branch_id()))->firstOrFail();
        $grade->update(['branch_id'=>$branchId,'name'=>$validated['name'],'grade_point'=>$validated['grade_point'],'lower_mark'=>$validated['lower_mark'],'upper_mark'=>$validated['upper_mark'],'remark'=>$validated['remark'] ?? '']);
        set_alert('success', translate('information_has_been_updated_successfully'));
        if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success','url'=>route('exam.grade')]);
        return redirect()->route('exam.grade');
    }
    public function gradeDelete(Request $request,int $id)
    {
        abort_unless(get_permission('exam_grade','is_delete'),403);
        $q=Grade::where('id',$id);
        if(!is_superadmin_loggedin()) $q->where('branch_id', get_loggedin_branch_id());
        $q->delete();
        if($request->expectsJson()||$request->ajax()) return response()->json(['status'=>'success']);
        set_alert('success', translate('information_has_been_deleted_successfully'));
        return back();
    }

    public function gradeEdit(int $id)
    {
        abort_unless(get_permission('exam_grade','is_edit'),403);
        $grade=Grade::where('id',$id)->when(!is_superadmin_loggedin(), fn($q)=>$q->where('branch_id',get_loggedin_branch_id()))->firstOrFail();
        $branches=is_superadmin_loggedin()?Branch::orderBy('name')->get():collect();
        return view('exam.grade_edit', ['title'=>translate('grades_range'),'grade'=>$grade,'branches'=>$branches]);
    }

    public function getDistributionByBranch(Request $request)
    {
        $branchId=$this->resolveBranchId($request);
        $result=DB::table('exam_mark_distribution')->where('branch_id',$branchId)->select('id','name')->get();
        $html='';
        foreach($result as $row) $html.='<option value="'.$row->id.'">'.e($row->name).'</option>';
        return response($html);
    }

    // ---- Report Card (bulk + individual) ----
    public function reportCard(Request $request)
    {
        abort_unless(get_permission('report_card','is_view'),403,translate('access_denied'));
        $branchId=$this->resolveBranchId($request);
        $branches=is_superadmin_loggedin()?Branch::orderBy('name')->get():collect();
        $sessions=DB::table('schoolyear')->orderBy('id','desc')->get();
        $classes=$branchId?DB::table('class')->where('branch_id',$branchId)->get():collect();
        $exams=$branchId?DB::table('exam')->where('branch_id',$branchId)->where('session_id',get_session_id())->get():collect();
        return view('exam.report_card', [
            'title'=>translate('report_card'),
            'branches'=>$branches,
            'sessions'=>$sessions,
            'classes'=>$classes,
            'exams'=>$exams,
            'branch_id'=>$branchId,
        ]);
    }

    public function reportCardPrint(Request $request)
    {
        abort_unless(get_permission('report_card','is_view'),403,translate('access_denied'));
        // Support both student_id and student_array (legacy)
        $studentIds = $request->input('student_id', $request->input('student_array', []));
        if (!is_array($studentIds)) $studentIds = [$studentIds];
        $studentIds = array_filter(array_map('intval', $studentIds));
        $request->merge(['student_id' => $studentIds]);
        $request->validate([
            'branch_id'=>'nullable|integer','session_id'=>'required|integer','exam_id'=>'required|integer',
            'student_id'=>'required|array','student_id.*'=>'integer','template_type'=>'nullable|in:semester,period,end_of_year',
            'remarks'=>'nullable|array','grade_scale'=>'nullable','attendance'=>'nullable',
        ]);
        $branchId=$this->resolveBranchId($request);
        $sessionId=(int)$request->input('session_id');
        $examId=(int)$request->input('exam_id');
        $remarks=$request->input('remarks',[]);
        $gradeScale=$request->boolean('grade_scale');
        $attendance=$request->boolean('attendance');
        $templateType=$request->input('template_type','semester');

        $template=ReportCardTemplate::where('branch_id',$branchId)->first();
        $frontSettings=[];
        if($template && !empty($template->front_layout_settings)){
            $frontSettings=is_array($template->front_layout_settings)?$template->front_layout_settings:json_decode($template->front_layout_settings,true);
        }
        $frontSettings=is_array($frontSettings)?$frontSettings:ReportCardTemplate::defaultLayout();

        // ranks if needed
        $studentRanks=[];
        if(!empty($frontSettings['show_class_rank'])){
            $studentRanks=$this->getStudentExamRanks($studentIds,$examId,$sessionId,$branchId);
        }
        $canvasLayouts=['front_canvas_layout'=>null,'back_canvas_layout'=>null];
        if($template){
            $canvasLayouts=['front_canvas_layout'=>$template->front_canvas_layout,'back_canvas_layout'=>$template->back_canvas_layout];
        }
        $grapejsDesign=['front'=>null,'back'=>null,'orientation'=>'portrait'];
        if($template && !empty($template->grapejs_json)){
            $g=is_array($template->grapejs_json)?$template->grapejs_json:json_decode($template->grapejs_json,true);
            if(isset($g['html'])){
                $grapejsDesign=$g;
            } else {
                $grapejsDesign=['front'=>$template->grapejs_html ?? null,'back'=>null,'orientation'=>$template->orientation ?? 'portrait'];
                if(!empty($template->grapejs_html)) $grapejsDesign['front']=['html'=>$template->grapejs_html,'css'=>$template->grapejs_css ?? ''];
            }
        } elseif($template && !empty($template->grapejs_html)){
            $grapejsDesign=['front'=>['html'=>$template->grapejs_html,'css'=>$template->grapejs_css ?? ''],'back'=>null,'orientation'=>$template->orientation ?? 'portrait'];
        }

        return view('exam.reportCard', [
            'student_array'=>$studentIds,
            'remarks_array'=>$remarks,
            'grade_scale'=>(int)$gradeScale,
            'attendance'=>(int)$attendance,
            'print_date'=>now()->toDateString(),
            'examID'=>$examId,
            'sessionID'=>$sessionId,
            'branchID'=>$branchId,
            'template'=>$template ? $template->toArray() : ['front_layout_settings'=>json_encode($frontSettings),'enable_back_page'=>0,'back_page_content'=>''],
            'canvas_layouts'=>$canvasLayouts,
            'grapejs_design'=>$grapejsDesign,
            'template_type'=>$templateType,
            'student_ranks'=>$studentRanks,
        ]);
    }

    public function reportCardStudents(Request $request)
    {
        $branchId = $this->resolveBranchId($request);
        $sessionId = (int)($request->input('session_id') ?: get_session_id());
        $classId = (int)$request->input('class_id');
        $sectionId = (int)$request->input('section_id');
        $examId = (int)$request->input('exam_id');
        abort_unless($classId && $sectionId, 422);
        $students = DB::table('enroll as en')
            ->join('student as s','s.id','=','en.student_id')
            ->join('login_credential as l', function($j){ $j->on('l.user_id','=','s.id')->where('l.role',7); })
            ->leftJoin('class as c','c.id','=','en.class_id')
            ->leftJoin('section as se','se.id','=','en.section_id')
            ->where('en.branch_id',$branchId)->where('en.session_id',$sessionId)
            ->where('en.class_id',$classId)->where('en.section_id',$sectionId)
            ->where('l.active',1)
            ->select('en.student_id','s.first_name','s.last_name','s.register_no','en.roll','c.name as class_name','se.name as section_name')
            ->orderBy('en.roll')->get();
        return response()->json($students);
    }

    public function getStudentReportCard(int $studentID, int $examID, int $sessionID): array
    {
        $student=DB::table('enroll as en')
            ->join('student as s','s.id','=','en.student_id')
            ->join('login_credential as l', function($j){ $j->on('l.user_id','=','s.id')->where('l.role',7); })
            ->leftJoin('class as c','c.id','=','en.class_id')
            ->leftJoin('section as se','se.id','=','en.section_id')
            ->leftJoin('parent as p','p.id','=','s.parent_id')
            ->where('l.active',1)->where('en.student_id',$studentID)
            ->select('en.roll','s.*','c.name as class_name','se.name as section_name','en.class_id','en.section_id','p.father_name','p.mother_name')
            ->first();
        $studentArr=$student?(array)$student:[];
        $marks=DB::table('mark as m')
            ->leftJoin('subject as sub','sub.id','=','m.subject_id')
            ->leftJoin('timetable_exam as te', function($j){ $j->on('te.exam_id','=','m.exam_id')->on('te.class_id','=','m.class_id')->on('te.section_id','=','m.section_id')->on('te.subject_id','=','m.subject_id'); })
            ->where('m.exam_id',$examID)->where('m.student_id',$studentID)->where('m.session_id',$sessionID)
            ->select('m.mark as get_mark','m.absent as get_abs','sub.name as subject_name','te.mark_distribution')->get()->map(fn($r)=>(array)$r)->toArray();
        return ['student'=>$studentArr,'exam'=>$marks];
    }

    private function getStudentExamRanks(array $studentIDs, int $examID, int $sessionID, int $branchID): array
    {
        if(empty($studentIDs)) return [];
        $rows=DB::table('mark as m')->leftJoin('timetable_exam as te', function($j){ $j->on('te.exam_id','=','m.exam_id')->on('te.class_id','=','m.class_id')->on('te.section_id','=','m.section_id')->on('te.subject_id','=','m.subject_id'); })
            ->where('m.exam_id',$examID)->where('m.session_id',$sessionID)->whereIn('m.student_id',$studentIDs)->select('m.student_id','m.mark','m.absent','te.mark_distribution')->get()->toArray();
        $scores=[];
        foreach($rows as $row){
            $r=(array)$row;
            $sid=$r['student_id'];
            if(!isset($scores[$sid])) $scores[$sid]=['obtained'=>0,'full'=>0];
            $mark=(float)($r['mark'] ?? 0);
            $absent=!empty($r['absent']) && $r['absent']!=='0';
            $dist=json_decode($r['mark_distribution'] ?? '[]',true);
            $full=0;
            if(is_array($dist)) foreach($dist as $it) if(isset($it['full_mark'])) $full+= (float)$it['full_mark'];
            $scores[$sid]['obtained']+= $absent?0:$mark;
            $scores[$sid]['full']+= $full;
        }
        $percents=[];
        foreach($scores as $sid=>$s){ $percents[$sid]=$s['full']>0?($s['obtained']*100)/$s['full']:0; }
        arsort($percents);
        $ranks=[]; $rank=1; $prev=null; $pos=1;
        foreach($percents as $sid=>$pct){
            if($prev!==null && $pct < $prev) $rank=$pos;
            $ranks[$sid]=$rank. ' / '.count($percents);
            $prev=$pct; $pos++;
        }
        return $ranks;
    }

    private function getGrade(float $mark, int $branchId): ?array
    {
        $grades=DB::table('grade')->where('branch_id',$branchId)->get()->map(fn($r)=>(array)$r)->toArray();
        foreach($grades as $row){ if($mark >= $row['lower_mark'] && $mark <= $row['upper_mark']) return $row; }
        return null;
    }
}
