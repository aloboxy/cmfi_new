<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubjectController extends Controller
{
    public function index()
    {
        abort_unless(get_permission('subject', 'is_view'), 403, translate('access_denied'));

        $list = DB::table('subject as t')
            ->leftJoin('branch as b', 'b.id', '=', 't.branch_id')
            ->select('t.*', 'b.name as branch_name')
            ->when(! is_superadmin_loggedin(), fn ($q) => $q->where('t.branch_id', get_loggedin_branch_id()))
            ->orderBy('t.id', 'asc')
            ->get();

        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();

        return view('subject.index', [
            'title' => translate('subject'),
            'subjectlist' => $list,
            'branches' => $branches,
        ]);
    }

    public function edit(int $id)
    {
        abort_unless(get_permission('subject', 'is_edit'), 403, translate('access_denied'));

        $subject = DB::table('subject as t')
            ->leftJoin('branch as b', 'b.id', '=', 't.branch_id')
            ->select('t.*', 'b.name as branch_name')
            ->where('t.id', $id)
            ->when(! is_superadmin_loggedin(), fn ($q) => $q->where('t.branch_id', get_loggedin_branch_id()))
            ->first();

        abort_if(! $subject, 404);

        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();

        return view('subject.edit', [
            'title' => translate('subject'),
            'subject' => (array) $subject,
            'branches' => $branches,
        ]);
    }

    public function save(Request $request)
    {
        $subjectId = $request->input('subject_id');
        $isEdit = ! empty($subjectId);

        if (! $isEdit) {
            abort_unless(get_permission('subject', 'is_add'), 403, translate('access_denied'));
        } else {
            abort_unless(get_permission('subject', 'is_edit'), 403, translate('access_denied'));
        }

        $branchId = $this->resolveBranchId($request);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'subject_code' => ['required', 'string', 'max:200'],
            'subject_type' => ['required', 'string', 'max:255'],
            'subject_author' => ['nullable', 'string', 'max:255'],
        ];
        if (is_superadmin_loggedin()) {
            $rules['branch_id'] = ['required', 'integer', 'exists:branch,id'];
        }

        $validated = $request->validate($rules);

        $data = [
            'name' => $validated['name'],
            'subject_code' => $validated['subject_code'],
            'subject_type' => $validated['subject_type'],
            'subject_author' => $validated['subject_author'] ?? '',
            'branch_id' => $branchId,
        ];

        if (! $isEdit) {
            DB::table('subject')->insert($data);
            set_alert('success', translate('information_has_been_saved_successfully'));
        } else {
            $q = DB::table('subject')->where('id', $subjectId);
            if (! is_superadmin_loggedin()) {
                $q->where('branch_id', get_loggedin_branch_id());
            }
            $q->update($data);
            set_alert('success', translate('information_has_been_updated_successfully'));
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['status' => 'success', 'url' => route('subject.index')]);
        }

        return redirect()->route('subject.index');
    }

    public function destroy(Request $request, int $id)
    {
        abort_unless(get_permission('subject', 'is_delete'), 403, translate('access_denied'));

        if (! is_superadmin_loggedin()) {
            $exists = DB::table('subject')->where('id', $id)->where('branch_id', get_loggedin_branch_id())->exists();
            abort_unless($exists, 403, translate('access_denied'));
        }

        DB::table('subject')->where('id', $id)->delete();
        DB::table('subject_assign')->where('subject_id', $id)->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['status' => 'success']);
        }

        set_alert('success', translate('information_has_been_deleted_successfully'));
        return redirect()->route('subject.index');
    }

    // Class assign
    public function classAssign()
    {
        abort_unless(get_permission('subject_class_assign', 'is_view'), 403, translate('access_denied'));

        $branchId = $this->effectiveBranchId();

        $assignlist = DB::table('subject_assign as sa')
            ->leftJoin('branch as b', 'b.id', '=', 'sa.branch_id')
            ->leftJoin('class as c', 'c.id', '=', 'sa.class_id')
            ->leftJoin('section as s', 's.id', '=', 'sa.section_id')
            ->select('sa.class_id', 'sa.section_id', 'sa.branch_id', 'b.name as branch_name', 'c.name as class_name', 's.name as section_name')
            ->groupBy('sa.class_id', 'sa.section_id', 'sa.branch_id', 'b.name', 'c.name', 's.name')
            ->when(! is_superadmin_loggedin(), fn ($q) => $q->where('sa.branch_id', get_loggedin_branch_id()))
            ->get();

        $classes = $branchId ? SchoolClass::where('branch_id', $branchId)->orderBy('name')->get() : collect();
        $subjects = $branchId ? Subject::where('branch_id', $branchId)->orderBy('name')->get() : collect();
        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();

        return view('subject.class_assign', [
            'title' => translate('class_assign'),
            'assignlist' => $assignlist,
            'branch_id' => $branchId,
            'classes' => $classes,
            'subjects' => $subjects,
            'branches' => $branches,
        ]);
    }

    public function classAssignSave(Request $request)
    {
        abort_unless(get_permission('subject_class_assign', 'is_add'), 403, translate('access_denied'));

        $branchId = $this->resolveBranchId($request);

        $rules = [
            'class_id' => ['required', 'integer', 'exists:class,id'],
            'section_id' => ['required', 'integer'],
            'subjects' => ['required', 'array', 'min:1'],
            'subjects.*' => ['integer', 'exists:subject,id'],
        ];
        if (is_superadmin_loggedin()) {
            $rules['branch_id'] = ['required', 'integer', 'exists:branch,id'];
        }

        $validated = $request->validate($rules);

        // unique check
        $exists = DB::table('subject_assign')
            ->where('class_id', $validated['class_id'])
            ->where('section_id', $validated['section_id'])
            ->where('session_id', get_session_id())
            ->where('branch_id', $branchId)
            ->exists();

        if ($exists) {
            $msg = 'This class and section is already assigned.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['status' => 'fail', 'error' => ['class_id' => $msg]]);
            }
            return back()->withErrors(['class_id' => $msg])->withInput();
        }

        // get class teacher
        $teacherAllocation = DB::table('teacher_allocation')
            ->where('class_id', $validated['class_id'])
            ->where('section_id', $validated['section_id'])
            ->where('session_id', get_session_id())
            ->where('branch_id', $branchId)
            ->first();

        $teacherId = $teacherAllocation ? $teacherAllocation->teacher_id : 0;

        foreach ($validated['subjects'] as $subjectId) {
            $where = [
                'class_id' => $validated['class_id'],
                'section_id' => $validated['section_id'],
                'subject_id' => $subjectId,
                'session_id' => get_session_id(),
                'branch_id' => $branchId,
            ];
            $already = DB::table('subject_assign')->where($where)->exists();
            if (! $already) {
                $where['teacher_id'] = $teacherId;
                DB::table('subject_assign')->insert($where);
            }
        }

        set_alert('success', translate('information_has_been_saved_successfully'));

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['status' => 'success', 'url' => route('subject.class_assign')]);
        }

        return redirect()->route('subject.class_assign');
    }

    public function classAssignEdit(Request $request)
    {
        abort_unless(get_permission('subject_class_assign', 'is_edit'), 403, translate('access_denied'));

        $branchId = $this->resolveBranchId($request);
        $sessionId = get_session_id();

        $validated = $request->validate([
            'class_id' => ['required', 'integer', 'exists:class,id'],
            'section_id' => ['required', 'integer'],
            'subjects' => ['required', 'array', 'min:1'],
            'subjects.*' => ['integer', 'exists:subject,id'],
        ]);

        $classId = $validated['class_id'];
        $sectionId = $validated['section_id'];

        $teacherAllocation = DB::table('teacher_allocation')
            ->where('class_id', $classId)
            ->where('section_id', $sectionId)
            ->where('session_id', $sessionId)
            ->where('branch_id', $branchId)
            ->first();

        $teacherId = $teacherAllocation ? $teacherAllocation->teacher_id : 0;

        foreach ($validated['subjects'] as $subjectId) {
            $where = [
                'class_id' => $classId,
                'section_id' => $sectionId,
                'subject_id' => $subjectId,
                'session_id' => $sessionId,
                'branch_id' => $branchId,
            ];
            if (! DB::table('subject_assign')->where($where)->exists()) {
                $where['teacher_id'] = $teacherId;
                DB::table('subject_assign')->insert($where);
            }
        }

        // delete removed subjects
        DB::table('subject_assign')
            ->where('class_id', $classId)
            ->where('section_id', $sectionId)
            ->where('session_id', $sessionId)
            ->where('branch_id', $branchId)
            ->whereNotIn('subject_id', $validated['subjects'])
            ->delete();

        set_alert('success', translate('information_has_been_updated_successfully'));

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['status' => 'success', 'url' => route('subject.class_assign')]);
        }

        return redirect()->route('subject.class_assign');
    }

    public function classAssignDelete(Request $request, int $classId, int $sectionId)
    {
        abort_unless(get_permission('subject_class_assign', 'is_delete'), 403, translate('access_denied'));

        $q = DB::table('subject_assign')
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
        return redirect()->route('subject.class_assign');
    }

    // Teacher assign
    public function teacherAssign(Request $request)
    {
        abort_unless(get_permission('subject_teacher_assign', 'is_view'), 403, translate('access_denied'));

        if ($request->isMethod('post')) {
            abort_unless(get_permission('subject_teacher_assign', 'is_add'), 403, translate('access_denied'));

            $branchId = $this->resolveBranchId($request);

            $rules = [
                'staff_id' => ['required', 'integer', 'exists:staff,id'],
                'class_id' => ['required', 'integer', 'exists:class,id'],
                'section_id' => ['required', 'integer'],
                'subject_id' => ['required', 'integer', 'exists:subject,id'],
            ];
            if (is_superadmin_loggedin()) {
                $rules['branch_id'] = ['required', 'integer', 'exists:branch,id'];
            }

            $validated = $request->validate($rules);

            $where = [
                'class_id' => $validated['class_id'],
                'section_id' => $validated['section_id'],
                'subject_id' => $validated['subject_id'],
                'session_id' => get_session_id(),
                'branch_id' => $branchId,
            ];

            $row = DB::table('subject_assign')->where($where)->first();
            if ($row) {
                DB::table('subject_assign')->where('id', $row->id)->update(['teacher_id' => $validated['staff_id']]);
            }

            set_alert('success', translate('information_has_been_updated_successfully'));

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['status' => 'success', 'url' => route('subject.teacher_assign')]);
            }

            return redirect()->route('subject.teacher_assign');
        }

        $branchId = $this->effectiveBranchId();

        $assignlist = DB::table('subject_assign as sa')
            ->leftJoin('class as c', 'c.id', '=', 'sa.class_id')
            ->leftJoin('section as s', 's.id', '=', 'sa.section_id')
            ->leftJoin('subject as sb', 'sb.id', '=', 'sa.subject_id')
            ->leftJoin('staff as t', 't.id', '=', 'sa.teacher_id')
            ->leftJoin('staff_department as sd', 'sd.id', '=', 't.department')
            ->select('sa.*', 'c.name as class_name', 's.name as section_name', 'sb.name as subject_name', 't.name as teacher_name', 't.department', 'sd.name as department_name')
            ->where('sa.teacher_id', '!=', 0)
            ->when(! is_superadmin_loggedin(), fn ($q) => $q->where('sa.branch_id', get_loggedin_branch_id()))
            ->orderBy('sa.id', 'asc')
            ->get();

        $classes = $branchId ? SchoolClass::where('branch_id', $branchId)->orderBy('name')->get() : collect();
        $teachers = $branchId ? $this->getStaffList($branchId, 3) : collect();
        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();

        return view('subject.teacher_assign', [
            'title' => translate('teacher_assign'),
            'assignlist' => $assignlist,
            'branch_id' => $branchId,
            'classes' => $classes,
            'teachers' => $teachers,
            'branches' => $branches,
        ]);
    }

    public function teacherAssignDelete(Request $request, int $id)
    {
        abort_unless(get_permission('subject_teacher_assign', 'is_delete'), 403, translate('access_denied'));

        $q = DB::table('subject_assign')->where('id', $id);
        if (! is_superadmin_loggedin()) {
            $q->where('branch_id', get_loggedin_branch_id());
        }
        $q->update(['teacher_id' => 0]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['status' => 'success']);
        }

        set_alert('success', translate('information_has_been_deleted_successfully'));
        return redirect()->route('subject.teacher_assign');
    }

    public function getByClassSection(Request $request)
    {
        $classId = $request->input('classID');
        $sectionId = $request->input('sectionID');

        if (empty($classId)) {
            return response('<option value="">' . translate('select') . '</option>');
        }

        $rows = DB::table('subject_assign')
            ->where('class_id', $classId)
            ->where('section_id', $sectionId)
            ->select('id', 'subject_id')
            ->get();

        if ($rows->isEmpty()) {
            return response('<option value="">' . translate('no_information_available') . '</option>');
        }

        $html = '<option value="">' . translate('select') . '</option>';
        foreach ($rows as $row) {
            $name = get_type_name_by_id('subject', $row->subject_id);
            $html .= '<option value="' . $row->subject_id . '">' . e($name) . '</option>';
        }

        return response($html);
    }

    public function getClassAssignModal(Request $request)
    {
        $classId = $request->input('class_id');
        $sectionId = $request->input('section_id');
        $branchId = get_type_name_by_id('class', $classId, 'branch_id');
        $subjects = DB::table('subject')->where('branch_id', $branchId)->get();

        $html = '';
        foreach ($subjects as $row) {
            $assigned = DB::table('subject_assign')
                ->where('class_id', $classId)
                ->where('section_id', $sectionId)
                ->where('session_id', get_session_id())
                ->where('subject_id', $row->id)
                ->exists();
            $html .= '<option value="' . $row->id . '"' . ($assigned ? ' selected' : '') . '>' . e($row->name) . '</option>';
        }

        return response()->json([
            'branch_id' => $branchId,
            'class_id' => $classId,
            'section_id' => $sectionId,
            'subject' => $html,
        ]);
    }

    private function resolveBranchId(Request $request): int
    {
        if (is_superadmin_loggedin()) {
            return (int) $request->input('branch_id');
        }
        return (int) get_loggedin_branch_id();
    }

    private function effectiveBranchId(): ?int
    {
        if (is_superadmin_loggedin()) {
            $bid = request()->input('branch_id');
            if ($bid) {
                return (int) $bid;
            }
            // fallback to first branch or null
            return null;
        }
        return (int) get_loggedin_branch_id();
    }

    private function getStaffList($branchId, $role)
    {
        if (empty($branchId)) {
            return collect();
        }
        return DB::table('staff as s')
            ->join('login_credential as l', function ($join) {
                $join->on('l.user_id', '=', 's.id')->whereNotIn('l.role', [6, 7]);
            })
            ->where('s.branch_id', $branchId)
            ->where('l.role', $role)
            ->select('s.id', 's.name', 's.staff_id')
            ->get()
            ->mapWithKeys(fn ($r) => [$r->id => $r->name . ' (' . $r->staff_id . ')']);
    }
}
