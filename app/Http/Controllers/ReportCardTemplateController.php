<?php

namespace App\Http\Controllers;

use App\Models\ReportCardTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportCardTemplateController extends Controller
{
    private function resolveBranchId(Request $request): int
    {
        $bid = $request->input('branch_id') ?: get_loggedin_branch_id();
        if (!$bid) {
            $bid = DB::table('branch')->orderBy('id')->value('id') ?? 1;
        }
        return (int) $bid;
    }

    public function index(Request $request)
    {
        abort_unless(get_permission('school_settings', 'is_view'), 403);
        $branchId = $this->resolveBranchId($request);
        $template = ReportCardTemplate::firstOrCreate(
            ['branch_id' => $branchId],
            ['front_layout_settings' => ReportCardTemplate::defaultLayout(), 'enable_back_page' => false]
        );
        // Ensure front_layout_settings is array
        if (is_string($template->front_layout_settings)) {
            $template->front_layout_settings = json_decode($template->front_layout_settings, true) ?: ReportCardTemplate::defaultLayout();
        }
        return view('settings.report_card_template', [
            'title' => translate('report_card_template'),
            'template' => $template,
            'branch_id' => $branchId,
            'branches' => DB::table('branch')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(get_permission('school_settings', 'is_edit'), 403);
        $branchId = $this->resolveBranchId($request);
        $front = [
            'show_gpa' => $request->boolean('show_gpa') ? 1 : 0,
            'show_class_rank' => $request->boolean('show_class_rank') ? 1 : 0,
            'show_attendance_summary' => $request->boolean('show_attendance_summary') ? 1 : 0,
            'show_vpa_signature_line' => $request->boolean('show_vpa_signature_line') ? 1 : 0,
            'show_principal_signature_line' => $request->boolean('show_principal_signature_line') ? 1 : 0,
            'grading_scale_legend' => $request->boolean('grading_scale_legend') ? 1 : 0,
            'order' => [],
            'titles' => [
                'student_info' => $request->input('title_student_info', 'Student Information'),
                'subjects_table' => $request->input('title_subjects_table', 'Academic Performance'),
                'attendance_summary' => $request->input('title_attendance_summary', 'Attendance Summary'),
                'gpa' => $request->input('title_gpa', 'GPA'),
                'class_rank' => $request->input('title_class_rank', 'Class Rank'),
                'grade_scale_legend' => $request->input('title_grade_scale_legend', 'Grading Scale'),
                'signature_lines' => $request->input('title_signature_lines', 'Signature Lines'),
            ],
        ];
        $keys = ['student_info','subjects_table','attendance_summary','gpa','class_rank','grade_scale_legend','signature_lines'];
        $orderMap = [];
        foreach ($keys as $k) {
            $orderMap[$k] = (int) $request->input('order_'.$k, 0);
        }
        asort($orderMap);
        foreach ($orderMap as $k => $v) {
            if ($v > 0) $front['order'][] = $k;
        }
        if (empty($front['order'])) {
            $front['order'] = ReportCardTemplate::defaultLayout()['order'];
        }

        ReportCardTemplate::updateOrCreate(
            ['branch_id' => $branchId],
            [
                'front_layout_settings' => $front,
                'enable_back_page' => $request->boolean('enable_back_page'),
                'back_page_content' => $request->input('back_page_content'),
            ]
        );
        set_alert('success', translate('information_has_been_updated_successfully'));
        return redirect()->route('settings.report_card_template', ['branch_id' => $branchId]);
    }

    public function saveGrapejs(Request $request)
    {
        abort_unless(get_permission('school_settings', 'is_edit'), 403);
        $branchId = $this->resolveBranchId($request);
        $request->validate([
            'design_data' => 'nullable|string',
            'orientation' => 'nullable|in:portrait,landscape',
        ]);
        $design = null;
        if ($request->filled('design_data')) {
            $design = json_decode($request->input('design_data'), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json(['status' => 'error', 'message' => 'Invalid JSON: '.json_last_error_msg()], 422);
            }
        }
        $template = ReportCardTemplate::firstOrCreate(
            ['branch_id' => $branchId],
            ['front_layout_settings' => ReportCardTemplate::defaultLayout()]
        );
        $template->update([
            'grapejs_html' => $design['html'] ?? $request->input('grapejs_html'),
            'grapejs_css' => $design['css'] ?? $request->input('grapejs_css'),
            'grapejs_json' => $design ?? $request->input('grapejs_json'),
            'orientation' => $request->input('orientation', $template->orientation ?? 'portrait'),
        ]);
        return response()->json(['status' => 'success', 'message' => translate('information_has_been_saved_successfully')]);
    }

    public function saveCanvas(Request $request)
    {
        abort_unless(get_permission('school_settings', 'is_edit'), 403);
        $branchId = $this->resolveBranchId($request);
        $template = ReportCardTemplate::firstOrCreate(
            ['branch_id' => $branchId],
            ['front_layout_settings' => ReportCardTemplate::defaultLayout()]
        );
        $template->update([
            'front_canvas_layout' => $request->input('front_canvas_layout'),
            'back_canvas_layout' => $request->input('back_canvas_layout'),
        ]);
        return response()->json(['status' => 'success', 'message' => translate('information_has_been_saved_successfully')]);
    }

    public function preview(Request $request, int $branchId = 0)
    {
        abort_unless(get_permission('school_settings', 'is_view'), 403);
        $branchId = $branchId ?: $this->resolveBranchId($request);
        $template = ReportCardTemplate::where('branch_id', $branchId)->first();
        // Mock student data for preview
        $mock = [
            'student_name' => 'John Doe',
            'class' => 'Grade 10 - A',
            'roll' => '001',
            'subjects' => [
                ['name' => 'Mathematics', 'marks' => 85, 'grade' => 'A'],
                ['name' => 'Science', 'marks' => 78, 'grade' => 'B+'],
                ['name' => 'English', 'marks' => 92, 'grade' => 'A+'],
            ],
            'gpa' => '3.85',
            'rank' => '5th / 45',
            'attendance' => '96%',
        ];
        return view('settings.report_card_preview', compact('template', 'mock', 'branchId'));
    }
}
