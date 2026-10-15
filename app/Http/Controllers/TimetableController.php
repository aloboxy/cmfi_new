<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TimetableController extends Controller
{
    public function viewClass(Request $request)
    {
        abort_unless(get_permission('class_timetable', 'is_view'), 403, translate('access_denied'));

        $branchId = $this->resolveBranchId($request);
        $timetables = null;
        $classId = null;
        $sectionId = null;

        if ($request->isMethod('post')) {
            $request->validate([
                'class_id' => ['required', 'integer', 'exists:class,id'],
                'section_id' => ['required'],
            ]);
            $classId = $request->input('class_id');
            $sectionId = $request->input('section_id');

            $timetables = DB::table('timetable_class')
                ->where('branch_id', $branchId)
                ->where('class_id', $classId)
                ->where('section_id', $sectionId)
                ->where('session_id', get_session_id())
                ->orderBy('time_start', 'asc')
                ->get();
        }

        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();
        $effectiveBranchId = $branchId;
        $classes = $effectiveBranchId ? SchoolClass::where('branch_id', $effectiveBranchId)->orderBy('name')->get() : collect();

        return view('timetable.viewclass', [
            'title' => translate('class') . ' ' . translate('schedule'),
            'branch_id' => $branchId,
            'branches' => $branches,
            'classes' => $classes,
            'timetables' => $timetables,
            'class_id' => $classId,
            'section_id' => $sectionId,
        ]);
    }

    public function setClasswise(Request $request)
    {
        abort_unless(get_permission('class_timetable', 'is_add'), 403, translate('access_denied'));

        $branchId = $this->resolveBranchId($request);
        $classId = null;
        $sectionId = null;
        $day = null;

        if ($request->isMethod('post')) {
            $classId = $request->input('class_id');
            $sectionId = $request->input('section_id');
            $day = $request->input('day');
        }

        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();
        $classes = $branchId ? SchoolClass::where('branch_id', $branchId)->orderBy('name')->get() : collect();

        // subject assign for dropdown when filtering
        $subjectAssign = collect();
        $teachers = collect();
        if ($classId && $sectionId && $branchId) {
            $subjectAssign = DB::table('subject_assign')
                ->where('class_id', $classId)
                ->where('section_id', $sectionId)
                ->where('session_id', get_session_id())
                ->where('branch_id', $branchId)
                ->get();

            $teachers = DB::table('staff as s')
                ->join('login_credential as l', function ($join) {
                    $join->on('l.user_id', '=', 's.id')->where('l.role', 3);
                })
                ->where('s.branch_id', $branchId)
                ->select('s.id', 's.name', 's.staff_id')
                ->get()
                ->mapWithKeys(fn ($r) => [$r->id => $r->name . ' (' . $r->staff_id . ')']);
        }

        return view('timetable.set_classwise', [
            'title' => translate('add') . ' ' . translate('schedule'),
            'branch_id' => $branchId,
            'branches' => $branches,
            'classes' => $classes,
            'class_id' => $classId,
            'section_id' => $sectionId,
            'day' => $day,
            'subjectAssign' => $subjectAssign,
            'teachers' => $teachers,
        ]);
    }

    public function updateClasswise(Request $request)
    {
        abort_unless(get_permission('class_timetable', 'is_edit'), 403, translate('access_denied'));

        $branchId = $this->resolveBranchId($request);
        $classId = $request->input('class_id');
        $sectionId = $request->input('section_id');
        $day = $request->input('day');

        $timetables = DB::table('timetable_class')
            ->where('branch_id', $branchId)
            ->where('class_id', $classId)
            ->where('section_id', $sectionId)
            ->where('day', $day)
            ->where('session_id', get_session_id())
            ->orderBy('time_start', 'asc')
            ->get();

        $teachers = DB::table('staff as s')
            ->join('login_credential as l', function ($join) {
                $join->on('l.user_id', '=', 's.id')->where('l.role', 3);
            })
            ->where('s.branch_id', $branchId)
            ->select('s.id', 's.name', 's.staff_id')
            ->get()
            ->mapWithKeys(fn ($r) => [$r->id => $r->name . ' (' . $r->staff_id . ')']);

        $subjects = DB::table('subject')->where('branch_id', $branchId)->get();

        return view('timetable.update_classwise', [
            'title' => translate('class') . ' ' . translate('schedule'),
            'branch_id' => $branchId,
            'class_id' => $classId,
            'section_id' => $sectionId,
            'day' => $day,
            'timetables' => $timetables,
            'teachers' => $teachers,
            'subjects' => $subjects,
        ]);
    }

    public function classSave(Request $request, string $mode = 'new')
    {
        abort_unless(get_permission('class_timetable', 'is_add'), 403, translate('access_denied'));

        $items = $request->input('timetable', []);

        $rules = [];
        foreach ($items as $key => $value) {
            $rules["timetable.$key.time_start"] = ['required'];
            $rules["timetable.$key.time_end"] = ['required'];
            if (! isset($value['break'])) {
                $rules["timetable.$key.subject"] = ['required', 'integer'];
                $rules["timetable.$key.teacher"] = ['required', 'integer'];
            }
        }

        $validated = $request->validate($rules);

        $branchId = $this->resolveBranchId($request);
        $sectionId = $request->input('section_id');
        $classId = $request->input('class_id');
        $sessionId = get_session_id();
        $day = $request->input('day');

        $arrayItems = $request->input('timetable');

        foreach ($arrayItems as $key => $value) {
            if (! isset($value['break'])) {
                $subjectId = $value['subject'];
                $teacherId = $value['teacher'];
                $isBreak = 0;
            } else {
                $subjectId = 0;
                $teacherId = 0;
                $isBreak = 1;
            }
            $timeStart = date('H:i:s', strtotime($value['time_start']));
            $timeEnd = date('H:i:s', strtotime($value['time_end']));
            $roomNumber = $value['class_room'] ?? '';

            if (! empty($timeStart) && ! empty($timeEnd)) {
                $arrayRoutine = [
                    'class_id' => $classId,
                    'section_id' => $sectionId,
                    'subject_id' => $subjectId,
                    'teacher_id' => $teacherId,
                    'time_start' => $timeStart,
                    'time_end' => $timeEnd,
                    'class_room' => $roomNumber,
                    'session_id' => $sessionId,
                    'branch_id' => $branchId,
                    'break' => $isBreak,
                    'day' => $day,
                ];

                if ($mode === 'new') {
                    DB::table('timetable_class')->insert($arrayRoutine);
                } elseif ($mode === 'update') {
                    $ids = $request->input('i', []);
                    $id = $ids[$key] ?? null;
                    if ($id) {
                        DB::table('timetable_class')->where('id', $id)->update($arrayRoutine);
                    }
                }
            }
        }

        if ($mode === 'update') {
            $arrayI = $request->input('i', []);
            if (! empty($arrayI)) {
                DB::table('timetable_class')
                    ->whereNotIn('id', $arrayI)
                    ->where('class_id', $classId)
                    ->where('section_id', $sectionId)
                    ->where('day', $day)
                    ->where('session_id', $sessionId)
                    ->where('branch_id', $branchId)
                    ->delete();
            }
        }

        set_alert('success', translate('information_has_been_saved_successfully'));

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['status' => 'success', 'url' => route('timetable.set_classwise'), 'message' => translate('information_has_been_saved_successfully')]);
        }

        return redirect()->route('timetable.set_classwise');
    }

    // Exam timetable
    public function viewExam(Request $request)
    {
        abort_unless(get_permission('exam_timetable', 'is_view'), 403, translate('access_denied'));

        $branchId = $this->resolveBranchId($request);
        $examlist = null;

        if ($request->isMethod('post')) {
            $request->validate([
                'class_id' => ['required', 'integer'],
                'section_id' => ['required'],
            ]);
            $classId = $request->input('class_id');
            $sectionId = $request->input('section_id');

            $examlist = DB::table('timetable_exam as t')
                ->leftJoin('branch as b', 'b.id', '=', 't.branch_id')
                ->where('t.branch_id', $branchId)
                ->where('t.class_id', $classId)
                ->where('t.section_id', $sectionId)
                ->where('t.session_id', get_session_id())
                ->groupBy('t.id', 't.exam_id', 't.branch_id', 't.class_id', 't.section_id', 't.subject_id', 't.hall_id', 't.exam_date', 't.time_start', 't.time_end', 't.session_id', 't.created_at', 't.updated_at', 'b.name')
                ->select('t.*', 'b.name as branch_name')
                ->orderBy('t.id', 'asc')
                ->get();
        }

        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();
        $classes = $branchId ? SchoolClass::where('branch_id', $branchId)->orderBy('name')->get() : collect();

        return view('timetable.viewexam', [
            'title' => translate('exam') . ' ' . translate('schedule'),
            'branch_id' => $branchId,
            'branches' => $branches,
            'classes' => $classes,
            'examlist' => $examlist,
        ]);
    }

    public function setExamwise(Request $request)
    {
        abort_unless(get_permission('exam_timetable', 'is_add'), 403, translate('access_denied'));

        $branchId = $this->resolveBranchId($request);
        $subjectassign = null;
        $examId = null;
        $classId = null;
        $sectionId = null;

        if ($request->isMethod('post')) {
            $examId = $request->input('exam_id');
            $classId = $request->input('class_id');
            $sectionId = $request->input('section_id');

            // use model method logic
            $subjectassign = DB::select("SELECT sa.*, s.name as subject_name, te.time_start, te.time_end, te.hall_id, te.exam_date, te.mark_distribution FROM subject_assign as sa LEFT JOIN subject as s ON s.id = sa.subject_id LEFT JOIN timetable_exam as te ON te.class_id = sa.class_id and te.section_id = sa.section_id and te.subject_id = sa.subject_id and te.session_id = sa.session_id and te.exam_id = ? WHERE sa.class_id = ? AND sa.section_id = ? AND sa.branch_id = ? AND sa.session_id = ?", [$examId, $classId, $sectionId, $branchId, get_session_id()]);
        }

        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();
        $classes = $branchId ? SchoolClass::where('branch_id', $branchId)->orderBy('name')->get() : collect();
        $exams = $branchId ? DB::table('exam')->where('branch_id', $branchId)->where('session_id', get_session_id())->get() : collect();
        $halls = $branchId ? DB::table('exam_hall')->where('branch_id', $branchId)->get() : collect();
        $examDistribution = null;
        if (! empty($examId)) {
            $examRow = DB::table('exam')->where('id', $examId)->first();
            $examDistribution = $examRow ? json_decode($examRow->mark_distribution ?? '[]', true) : [];
        }

        return view('timetable.set_examwise', [
            'title' => translate('add') . ' ' . translate('schedule'),
            'branch_id' => $branchId,
            'branches' => $branches,
            'classes' => $classes,
            'exams' => $exams,
            'halls' => $halls,
            'exam_id' => $examId,
            'class_id' => $classId,
            'section_id' => $sectionId,
            'subjectassign' => $subjectassign,
            'examDistribution' => $examDistribution,
        ]);
    }

    public function examCreate(Request $request)
    {
        abort_unless(get_permission('exam_timetable', 'is_add'), 403, translate('access_denied'));

        $items = $request->input('timetable', []);

        $rules = [];
        foreach ($items as $key => $value) {
            $rules["timetable.$key.date"] = ['required', 'date'];
            $rules["timetable.$key.time_start"] = ['required'];
            $rules["timetable.$key.time_end"] = ['required'];
            $rules["timetable.$key.hall_id"] = ['required', 'integer'];
        }

        $validated = $request->validate($rules);

        $branchId = $this->resolveBranchId($request);
        $examId = $request->input('exam_id');
        $classId = $request->input('class_id');
        $sectionId = $request->input('section_id');
        $sessionId = get_session_id();

        // Validate hall capacity
        foreach ($items as $key => $value) {
            $hallId = $value['hall_id'];
            $seats = DB::table('exam_hall')->where('id', $hallId)->value('seats');
            $stuCount = DB::table('enroll')->where('class_id', $classId)->where('section_id', $sectionId)->where('session_id', $sessionId)->count();
            if ($seats !== null && $stuCount > $seats) {
                $msg = 'The seats capacity is exceeded.';
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['status' => 'fail', 'error' => ["timetable.$key.hall_id" => $msg]]);
                }
                return back()->withErrors(["timetable.$key.hall_id" => $msg])->withInput();
            }
        }

        foreach ($items as $key => $value) {
            $distribution = [];
            foreach ($value['full_mark'] ?? [] as $id => $mark) {
                $distribution[$id]['full_mark'] = $mark;
            }
            foreach ($value['pass_mark'] ?? [] as $id => $mark) {
                $distribution[$id]['pass_mark'] = $mark;
            }

            $arrayData = [
                'exam_id' => $examId,
                'class_id' => $classId,
                'section_id' => $sectionId,
                'subject_id' => $value['subject_id'],
                'time_start' => $value['time_start'],
                'time_end' => $value['time_end'],
                'hall_id' => $value['hall_id'],
                'exam_date' => $value['date'],
                'mark_distribution' => json_encode($distribution),
                'branch_id' => $branchId,
                'session_id' => $sessionId,
            ];

            $existing = DB::table('timetable_exam')
                ->where('exam_id', $examId)
                ->where('class_id', $classId)
                ->where('section_id', $sectionId)
                ->where('subject_id', $value['subject_id'])
                ->where('session_id', $sessionId)
                ->first();

            if ($existing) {
                DB::table('timetable_exam')->where('id', $existing->id)->update($arrayData);
            } else {
                DB::table('timetable_exam')->insert($arrayData);
            }
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['status' => 'success', 'message' => translate('information_has_been_saved_successfully')]);
        }

        set_alert('success', translate('information_has_been_saved_successfully'));
        return redirect()->route('timetable.set_examwise');
    }

    public function examDelete(Request $request, int $examId, int $classId, int $sectionId)
    {
        abort_unless(get_permission('exam_timetable', 'is_delete'), 403, translate('access_denied'));

        $q = DB::table('timetable_exam')
            ->where('exam_id', $examId)
            ->where('class_id', $classId)
            ->where('section_id', $sectionId)
            ->where('session_id', get_session_id());

        if (! is_superadmin_loggedin()) {
            $q->where('branch_id', get_loggedin_branch_id());
        }

        $q->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['status' => 'success']);
        }

        set_alert('success', translate('information_has_been_deleted_successfully'));
        return back();
    }

    public function getExamTimetableM(Request $request)
    {
        $examId = $request->input('exam_id');
        $classId = $request->input('class_id');
        $sectionId = $request->input('section_id');

        $timetables = DB::table('timetable_exam as t')
            ->leftJoin('subject as s', 's.id', '=', 't.subject_id')
            ->leftJoin('exam_hall as eh', 'eh.id', '=', 't.hall_id')
            ->select('t.*', 's.name as subject_name', 'eh.hall_no')
            ->where('t.exam_id', $examId)
            ->where('t.class_id', $classId)
            ->where('t.section_id', $sectionId)
            ->where('t.session_id', get_session_id())
            ->when(! is_superadmin_loggedin(), fn ($q) => $q->where('t.branch_id', get_loggedin_branch_id()))
            ->get();

        return view('timetable.examTimetableM', [
            'exam_id' => $examId,
            'class_id' => $classId,
            'section_id' => $sectionId,
            'timetables' => $timetables,
        ]);
    }

    private function resolveBranchId(Request $request): ?int
    {
        if (is_superadmin_loggedin()) {
            $bid = $request->input('branch_id');
            if ($bid) {
                return (int) $bid;
            }
            // also check posted branch_id via Application_model logic: post branch_id or get branch via query?
            // fallback to helper get_loggedin_branch_id or null (superadmin all)
            $fallback = $request->input('branch_id', request()->query('branch_id'));
            if (! empty($fallback)) {
                return (int) $fallback;
            }
            // if no branch selected, try to use first branch or keep null for queries that need branch filter
            $first = DB::table('branch')->value('id');
            return $first ? (int) $first : null;
        }
        return (int) get_loggedin_branch_id();
    }
}
