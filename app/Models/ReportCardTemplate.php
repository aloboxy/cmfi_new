<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportCardTemplate extends Model
{
    protected $table = 'report_card_templates';

    protected $fillable = [
        'branch_id',
        'front_layout_settings',
        'front_canvas_layout',
        'enable_back_page',
        'back_page_content',
        'back_canvas_layout',
        'grapejs_html',
        'grapejs_css',
        'grapejs_json',
        'orientation',
    ];

    protected $casts = [
        'front_layout_settings' => 'array',
        'front_canvas_layout' => 'array',
        'back_canvas_layout' => 'array',
        'grapejs_json' => 'array',
        'enable_back_page' => 'boolean',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public static function defaultLayout(): array
    {
        return [
            'show_gpa' => 1,
            'show_class_rank' => 1,
            'show_attendance_summary' => 1,
            'show_vpa_signature_line' => 1,
            'show_principal_signature_line' => 1,
            'grading_scale_legend' => 1,
            'order' => ['student_info','subjects_table','attendance_summary','gpa','class_rank','grade_scale_legend','signature_lines'],
            'titles' => [
                'student_info' => 'Student Information',
                'subjects_table' => 'Academic Performance',
                'attendance_summary' => 'Attendance Summary',
                'gpa' => 'GPA',
                'class_rank' => 'Class Rank',
                'grade_scale_legend' => 'Grading Scale',
                'signature_lines' => 'Signature Lines',
            ],
        ];
    }
}
