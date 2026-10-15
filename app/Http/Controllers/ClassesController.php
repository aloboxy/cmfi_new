<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\SectionAllocation;
use App\Models\Staff;
use App\Models\TeacherAllocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassesController extends Controller
{
    public function index()
    {
        abort_unless(get_permission('classes', 'is_view'), 403, translate('access_denied'));

        $classlist = DB::table('class as t')
            ->leftJoin('branch as b', 'b.id', '=', 't.branch_id')
            ->select('t.*', 'b.name as branch_name')
            ->when(! is_superadmin_loggedin(), fn ($q) => $q->where('t.branch_id', get_loggedin_branch_id()))
            ->orderBy('t.id', 'asc')
            ->get()
            ->toArray();

        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();

        // For non-superadmin, preload promotion class options and sections
        $branchId = is_superadmin_loggedin() ? null : get_loggedin_branch_id();
        $promotionClasses = [];
        $sections = [];
        if ($branchId) {
            $promotionClasses = SchoolClass::where('branch_id', $branchId)->orderBy('name')->get();
            $sections = Section::where('branch_id', $branchId)->orderBy('name')->get();
        }

        $useSections = $this->isSectionEnabled($branchId ?? get_loggedin_branch_id());

        return view('classes.index', [
            'title' => translate('control_classes'),
            'classlist' => $classlist,
            'branches' => $branches,
            'promotionClasses' => $promotionClasses,
            'sections' => $sections,
            'useSections' => $useSections,
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(get_permission('classes', 'is_add'), 403, translate('access_denied'));

        $branchId = $this->resolveBranchId($request);

        $request->validate([
            'branch_id' => is_superadmin_loggedin() ? ['required', 'integer', 'exists:branch,id'] : ['nullable'],
            'name' => ['required', 'string', 'max:255'],
            'name_numeric' => ['nullable', 'numeric'],
            'next_class_id' => ['nullable', 'integer', 'exists:class,id'],
            'sections' => ['nullable', 'array'],
            'sections.*' => ['integer', 'exists:section,id'],
        ]);

        $class = SchoolClass::create([
            'name' => $request->input('name'),
            'name_numeric' => $request->input('name_numeric') ?? '',
            'next_class_id' => $request->input('next_class_id') ?: null,
            'branch_id' => $branchId,
        ]);

        $classId = $class->id;

        if ($this->isSectionEnabled($branchId)) {
            $sections = $request->input('sections', []);
            if (! empty($sections)) {
                foreach ($sections as $sectionId) {
                    $exists = SectionAllocation::where('class_id', $classId)->where('section_id', $sectionId)->exists();
                    if (! $exists) {
                        SectionAllocation::create(['class_id' => $classId, 'section_id' => $sectionId]);
                    }
                }
            }
        }

        set_alert('success', translate('information_has_been_saved_successfully'));

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['status' => 'success', 'url' => route('classes.index')]);
        }

        return redirect()->route('classes.index');
    }

    public function edit(int $id)
    {
        abort_unless(get_permission('classes', 'is_edit'), 403, translate('access_denied'));

        $class = DB::table('class as t')
            ->leftJoin('branch as b', 'b.id', '=', 't.branch_id')
            ->select('t.*', 'b.name as branch_name')
            ->where('t.id', $id)
            ->when(! is_superadmin_loggedin(), fn ($q) => $q->where('t.branch_id', get_loggedin_branch_id()))
            ->first();

        abort_if(! $class, 404);

        $classArr = (array) $class;
        $branchId = $classArr['branch_id'];
        $allocations = SectionAllocation::where('class_id', $id)->pluck('section_id')->toArray();
        $sectionsForBranch = Section::where('branch_id', $branchId)->orderBy('name')->get();
        $promotionClasses = SchoolClass::where('branch_id', $branchId)->where('id', '!=', $id)->orderBy('name')->get();
        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();
        $useSections = $this->isSectionEnabled($branchId);

        return view('classes.edit', [
            'title' => translate('control_classes'),
            'class' => $classArr,
            'allocations' => $allocations,
            'sections' => $sectionsForBranch,
            'promotionClasses' => $promotionClasses,
            'branches' => $branches,
            'useSections' => $useSections,
        ]);
    }

    public function update(Request $request, int $id)
    {
        abort_unless(get_permission('classes', 'is_edit'), 403, translate('access_denied'));

        $branchId = $this->resolveBranchId($request);

        $request->validate([
            'branch_id' => is_superadmin_loggedin() ? ['required', 'integer', 'exists:branch,id'] : ['nullable'],
            'name' => ['required', 'string', 'max:255'],
            'name_numeric' => ['nullable', 'numeric'],
            'next_class_id' => ['nullable', 'integer', 'exists:class,id'],
            'sections' => ['nullable', 'array'],
            'sections.*' => ['integer', 'exists:section,id'],
        ]);

        $q = SchoolClass::where('id', $id);
        if (! is_superadmin_loggedin()) {
            $q->where('branch_id', get_loggedin_branch_id());
        }
        $class = $q->firstOrFail();

        $class->update([
            'name' => $request->input('name'),
            'name_numeric' => $request->input('name_numeric') ?? '',
            'next_class_id' => $request->input('next_class_id') ?: null,
            'branch_id' => $branchId,
        ]);

        if ($this->isSectionEnabled($branchId)) {
            $sections = $request->input('sections', []);
            if (! empty($sections)) {
                foreach ($sections as $sectionId) {
                    $exists = SectionAllocation::where('class_id', $id)->where('section_id', $sectionId)->exists();
                    if (! $exists) {
                        SectionAllocation::create(['class_id' => $id, 'section_id' => $sectionId]);
                    }
                }
                SectionAllocation::where('class_id', $id)->whereNotIn('section_id', $sections)->delete();
            } else {
                // If no sections provided but editing, we keep existing? Legacy only deletes when sections not empty.
                // If you want to clear, uncomment:
                // SectionAllocation::where('class_id', $id)->delete();
            }
        }

        set_alert('success', translate('information_has_been_updated_successfully'));

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['status' => 'success', 'url' => route('classes.index')]);
        }

        return redirect()->route('classes.index');
    }

    public function destroy(Request $request, int $id)
    {
        abort_unless(get_permission('classes', 'is_delete'), 403, translate('access_denied'));

        $q = SchoolClass::where('id', $id);
        if (! is_superadmin_loggedin()) {
            $q->where('branch_id', get_loggedin_branch_id());
        }
        $deleted = $q->delete();
        if ($deleted) {
            SectionAllocation::where('class_id', $id)->delete();
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['status' => 'success']);
        }

        set_alert('success', translate('information_has_been_deleted_successfully'));
        return redirect()->route('classes.index');
    }

    // Teacher allocation
    public function teacherAllocation()
    {
        abort_unless(get_permission('assign_class_teacher', 'is_view'), 403, translate('access_denied'));

        $branchId = is_superadmin_loggedin() ? (request()->input('branch_id') ? (int) request()->input('branch_id') : null) : get_loggedin_branch_id();
        // If superadmin without branch filter, show all; else filter
        $query = DB::table('teacher_allocation as ta')
            ->leftJoin('staff as st', 'st.id', '=', 'ta.teacher_id')
            ->leftJoin('class as c', 'c.id', '=', 'ta.class_id')
            ->leftJoin('section as s', 's.id', '=', 'ta.section_id')
            ->select('ta.*', 'st.name as teacher_name', 'st.staff_id as teacher_code', 'c.name as class_name', 'c.branch_id', 's.name as section_name')
            ->orderBy('ta.id', 'asc')
            ->where('ta.session_id', get_session_id());

        if (! empty($branchId)) {
            $query->where('c.branch_id', $branchId);
        } elseif (! is_superadmin_loggedin()) {
            $query->where('c.branch_id', get_loggedin_branch_id());
        }

        $allocations = $query->get();
        $branches = is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect();
        $effectiveBranchId = $branchId ?? get_loggedin_branch_id();

        // For create form dropdowns
        $classes = $effectiveBranchId ? SchoolClass::where('branch_id', $effectiveBranchId)->orderBy('name')->get() : collect();
        $teachers = $this->getStaffList($effectiveBranchId, 3);

        $useSections = $this->isSectionEnabled($effectiveBranchId);

        return view('classes.teacher_allocation', [
            'title' => translate('assign_class_teacher'),
            'allocations' => $allocations,
            'branches' => $branches,
            'branch_id' => $effectiveBranchId,
            'classes' => $classes,
            'teachers' => $teachers,
            'useSections' => $useSections,
        ]);
    }

    public function getAllocationTeacher(Request $request)
    {
        abort_unless(get_permission('assign_class_teacher', 'is_edit'), 403, translate('access_denied'));

        $id = $request->input('id');
        $allocation = DB::table('teacher_allocation')->where('id', $id)->first();
        abort_if(! $allocation, 404);

        $branchId = $allocation->branch_id;
        $classes = SchoolClass::where('branch_id', $branchId)->orderBy('name')->get();
        $teachers = $this->getStaffList($branchId, 3);

        // sections for this class
        $sections = DB::table('sections_allocation')
            ->join('section', 'section.id', '=', 'sections_allocation.section_id')
            ->where('sections_allocation.class_id', $allocation->class_id)
            ->select('section.id', 'section.name')
            ->get();

        return view('classes.tallocation_modal', [
            'allocation' => $allocation,
            'classes' => $classes,
            'teachers' => $teachers,
            'sections' => $sections,
            'branches' => is_superadmin_loggedin() ? Branch::orderBy('name')->get() : collect(),
        ]);
    }

    public function teacherAllocationSave(Request $request)
    {
        $branchId = $this->resolveBranchId($request);
        $useSections = $this->isSectionEnabled($branchId);

        $rules = [
            'class_id' => ['required', 'integer', 'exists:class,id'],
            'staff_id' => ['required', 'integer', 'exists:staff,id'],
        ];
        if (is_superadmin_loggedin()) {
            $rules['branch_id'] = ['required', 'integer', 'exists:branch,id'];
        }
        if ($useSections) {
            $rules['section_id'] = ['required', 'integer'];
        } else {
            $rules['section_id'] = ['nullable', 'integer'];
        }

        $validated = $request->validate($rules);

        $classId = $validated['class_id'];
        $sectionId = $validated['section_id'] ?? 0;
        $teacherId = $validated['staff_id'];
        $allocationId = $request->input('allocation_id');

        // unique_teacherID and unique_sectionID validation
        $qTeacher = TeacherAllocation::where('teacher_id', $teacherId)->where('class_id', $classId)->where('section_id', $sectionId);
        $qSection = TeacherAllocation::where('class_id', $classId)->where('section_id', $sectionId);
        if (! empty($allocationId)) {
            $qTeacher->where('id', '!=', $allocationId);
            $qSection->where('id', '!=', $allocationId);
        }
        if ($qTeacher->exists()) {
            $msg = translate('class_teachers_are_already_allocated_for_this_class');
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['status' => 'fail', 'error' => ['staff_id' => $msg]]);
            }
            return back()->withErrors(['staff_id' => $msg])->withInput();
        }
        if (! empty($sectionId) && $qSection->exists()) {
            $msg = translate('this_class_teacher_already_assigned');
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['status' => 'fail', 'error' => ['section_id' => $msg]]);
            }
            return back()->withErrors(['section_id' => $msg])->withInput();
        }

        $data = [
            'branch_id' => $branchId,
            'session_id' => get_session_id(),
            'class_id' => $classId,
            'section_id' => empty($sectionId) ? 0 : $sectionId,
            'teacher_id' => $teacherId,
        ];

        if (empty($allocationId)) {
            abort_unless(get_permission('assign_class_teacher', 'is_add'), 403, translate('access_denied'));
            TeacherAllocation::create($data);
            set_alert('success', translate('information_has_been_saved_successfully'));
        } else {
            abort_unless(get_permission('assign_class_teacher', 'is_edit'), 403, translate('access_denied'));
            TeacherAllocation::where('id', $allocationId)->update($data);
            set_alert('success', translate('information_has_been_updated_successfully'));
        }

        // update subject_assign as in legacy model
        $subjectAssignWhere = [
            'branch_id' => $branchId,
            'session_id' => get_session_id(),
            'class_id' => $classId,
            'section_id' => empty($sectionId) ? 0 : $sectionId,
        ];
        DB::table('subject_assign')->where($subjectAssignWhere)->update(['teacher_id' => $teacherId]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['status' => 'success', 'url' => route('classes.teacher_allocation')]);
        }

        return redirect()->route('classes.teacher_allocation');
    }

    public function teacherAllocationDelete(Request $request, int $id)
    {
        abort_unless(get_permission('assign_class_teacher', 'is_delete'), 403, translate('access_denied'));

        $q = TeacherAllocation::where('id', $id);
        if (! is_superadmin_loggedin()) {
            $q->where('branch_id', get_loggedin_branch_id());
        }
        $q->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['status' => 'success']);
        }

        set_alert('success', translate('information_has_been_deleted_successfully'));
        return redirect()->route('classes.teacher_allocation');
    }

    private function resolveBranchId(Request $request): int
    {
        if (is_superadmin_loggedin()) {
            $bid = $request->input('branch_id');
            if (! empty($bid)) {
                return (int) $bid;
            }
            // fallback to get_branch_id logic: post branch_id or null
            // For superadmin editing, branch comes from class's branch or request
            return (int) $request->input('branch_id', get_loggedin_branch_id() ?? 0);
        }
        return (int) get_loggedin_branch_id();
    }

    private function isSectionEnabled($branchId): bool
    {
        if (empty($branchId)) {
            return true;
        }
        $row = Branch::where('id', $branchId)->first(['use_section']);
        if (! $row) {
            return true;
        }
        return (bool) $row->use_section;
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
