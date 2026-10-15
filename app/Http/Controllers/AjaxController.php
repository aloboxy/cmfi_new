<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AjaxController extends Controller
{
    public function getClassByBranch(Request $request)
    {
        $branchId = is_superadmin_loggedin() ? $request->input('branch_id') : get_loggedin_branch_id();
        if (empty($branchId)) {
            $branchId = $request->input('branch_id');
        }
        if (empty($branchId)) {
            return response('<option value="">' . translate('select_branch_first') . '</option>');
        }

        $classes = DB::table('class')->where('branch_id', $branchId)->select('id', 'name')->get();
        if ($classes->isEmpty()) {
            return response('<option value="">' . translate('no_information_available') . '</option>');
        }

        $html = '<option value="">' . translate('select') . '</option>';
        foreach ($classes as $row) {
            $html .= '<option value="' . $row->id . '">' . e($row->name) . '</option>';
        }
        return response($html);
    }

    public function getSectionByClass(Request $request)
    {
        $classId = $request->input('class_id');
        $all = $request->input('all');
        $multi = $request->input('multi');

        if (empty($classId)) {
            return response('<option value="">' . translate('select_class_first') . '</option>');
        }

        if ((int) loggedin_role_id() === ROLE_TEACHER) {
            $result = DB::table('teacher_allocation')
                ->join('section', 'section.id', '=', 'teacher_allocation.section_id')
                ->where('teacher_allocation.class_id', $classId)
                ->where('teacher_allocation.teacher_id', get_loggedin_user_id())
                ->where('teacher_allocation.session_id', get_session_id())
                ->select('teacher_allocation.section_id', 'section.name')
                ->get();
        } else {
            $result = DB::table('sections_allocation')
                ->join('section', 'section.id', '=', 'sections_allocation.section_id')
                ->where('sections_allocation.class_id', $classId)
                ->select('sections_allocation.section_id', 'section.name')
                ->get();
        }

        if ($result->isEmpty()) {
            return response('<option value="">' . translate('no_selection_available') . '</option>');
        }

        $html = '';
        if ($multi == false || $multi === null || $multi === 'false') {
            $html .= '<option value="">' . translate('select') . '</option>';
        }
        if ($all == true || $all === 'true') {
            $html .= '<option value="all">' . translate('all_sections') . '</option>';
        }
        foreach ($result as $row) {
            $html .= '<option value="' . $row->section_id . '">' . e($row->name) . '</option>';
        }
        return response($html);
    }

    public function getSectionByBranch(Request $request)
    {
        $branchId = is_superadmin_loggedin() ? $request->input('branch_id') : get_loggedin_branch_id();
        if (empty($branchId)) {
            return response('<option value="">' . translate('select_branch_first') . '</option>');
        }
        $sections = DB::table('section')->where('branch_id', $branchId)->select('id', 'name')->get();
        if ($sections->isEmpty()) {
            return response('<option value="">' . translate('no_information_available') . '</option>');
        }
        $html = '<option value="">' . translate('select') . '</option>';
        foreach ($sections as $row) {
            $html .= '<option value="' . $row->id . '">' . e($row->name) . '</option>';
        }
        return response($html);
    }

    public function getDataByBranch(Request $request)
    {
        $table = $request->input('table');
        $branchId = is_superadmin_loggedin() ? $request->input('branch_id') : get_loggedin_branch_id();
        if (empty($branchId)) {
            // try resolve via helper like Application_model::get_branch_id()
            $branchId = $request->input('branch_id');
        }
        $allowed = ['subject', 'class', 'section', 'exam', 'exam_hall', 'student_category', 'staff_department'];
        if (! in_array($table, $allowed, true)) {
            return response('<option value="">' . translate('no_information_available') . '</option>');
        }
        if (empty($branchId)) {
            return response('<option value="">' . translate('select_branch_first') . '</option>');
        }
        $result = DB::table($table)->where('branch_id', $branchId)->select('id', 'name')->get();
        if ($result->isEmpty()) {
            return response('<option value="">' . translate('no_information_available') . '</option>');
        }
        $html = '<option value="">' . translate('select') . '</option>';
        foreach ($result as $row) {
            $html .= '<option value="' . $row->id . '">' . e($row->name) . '</option>';
        }
        return response($html);
    }

    public function getExamByBranch(Request $request)
    {
        $branchId = is_superadmin_loggedin() ? $request->input('branch_id') : get_loggedin_branch_id();
        if (empty($branchId)) {
            $branchId = $request->input('branch_id');
        }
        if (empty($branchId)) {
            return response('<option value="">' . translate('select_branch_first') . '</option>');
        }
        $result = DB::table('exam')->where('branch_id', $branchId)->where('session_id', get_session_id())->select('id', 'name', 'term_id')->get();
        if ($result->isEmpty()) {
            return response('<option value="">' . translate('no_information_available') . '</option>');
        }
        $html = '<option value="">' . translate('select') . '</option>';
        foreach ($result as $row) {
            if (! empty($row->term_id)) {
                $term = DB::table('exam_term')->where('id', $row->term_id)->value('name');
                $name = $row->name . ' (' . $term . ')';
            } else {
                $name = $row->name;
            }
            $html .= '<option value="' . $row->id . '">' . e($name) . '</option>';
        }
        return response($html);
    }

    public function getStaffListRole(Request $request)
    {
        $branchId = is_superadmin_loggedin() ? $request->input('branch_id') : get_loggedin_branch_id();
        if (empty($branchId)) {
            $branchId = $request->input('branch_id');
        }
        if (empty($branchId)) {
            return response('<option value="">' . translate('select_branch_first') . '</option>');
        }
        $roleId = $request->input('role_id');
        $staffId = $request->input('staff_id');
        $result = DB::table('staff')
            ->join('login_credential as lc', function ($join) {
                $join->on('lc.user_id', '=', 'staff.id')->whereNotIn('lc.role', [6, 7]);
            })
            ->where('lc.role', $roleId)
            ->where('staff.branch_id', $branchId)
            ->orderBy('staff.id', 'asc')
            ->select('staff.id', 'staff.name', 'staff.staff_id')
            ->get();

        if ($result->isEmpty()) {
            return response('<option value="">' . translate('no_information_available') . '</option>');
        }
        $html = '<option value="">' . translate('select') . '</option>';
        foreach ($result as $staff) {
            $selected = ((string) $staff->id === (string) $staffId ? ' selected' : '');
            $html .= '<option value="' . $staff->id . '"' . $selected . '>' . e($staff->name) . ' (' . e($staff->staff_id) . ')</option>';
        }
        return response($html);
    }

    public function getSubjectByClass(Request $request)
    {
        $classId = $request->input('classID') ?? $request->input('class_id');
        if (empty($classId)) {
            return response('<option value="">' . translate('select_class_first') . '</option>');
        }
        $query = DB::table('subject_assign')
            ->join('subject', 'subject.id', '=', 'subject_assign.subject_id')
            ->where('subject_assign.class_id', $classId);
        if (! is_superadmin_loggedin()) {
            $query->where('subject_assign.branch_id', get_loggedin_branch_id());
        }
        $subjects = $query->select('subject_assign.subject_id', 'subject.name', 'subject.subject_code')->get();
        if ($subjects->isEmpty()) {
            return response('<option value="">' . translate('no_information_available') . '</option>');
        }
        $html = '<option value="">' . translate('select') . '</option>';
        foreach ($subjects as $row) {
            $html .= '<option value="' . $row->subject_id . '">' . e($row->name) . ' (' . e($row->subject_code) . ')</option>';
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
}
