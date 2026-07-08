<?php
/**
 * Canvas-based Report Card Renderer
 * Renders blocks from GridStack visual editor layouts for front and back pages
 */

if (empty($student_array)) {
    return;
}

$getExam = $this->db->where(array('id' => $examID))->get('exam')->row_array();
$getSchool = $this->db->where(array('id' => $getExam['branch_id']))->get('branch')->row_array();
$schoolYear = get_type_name_by_id('schoolyear', $sessionID, 'school_year');

/**
 * Helper function to render a block based on type
 */
function render_canvas_block($block_type, $student, $student_id, $getMarksList, $getExam, $getSchool, $schoolYear, $student_ranks, $rankLabel) {
    $html = '';
    
    switch ($block_type) {
        case 'student_info':
            $html .= '<div class="canvas-block">';
            $html .= '<div class="canvas-block-title">Student Information</div>';
            $html .= '<table class="table table-bordered" style="margin-top: 10px;">';
            $html .= '<tbody>';
            $html .= '<tr><th>Name</th><td>' . $student['first_name'] . ' ' . $student['last_name'] . '</td>';
            $html .= '<th>Register No</th><td>' . $student['register_no'] . '</td>';
            $html .= '<th>Roll Number</th><td>' . $student['roll'] . '</td></tr>';
            $html .= '<tr><th>Father Name</th><td>' . $student['father_name'] . '</td>';
            $html .= '<th>Class</th><td>' . $student['class_name'] . ' (' . $student['section_name'] . ')</td>';
            $html .= '<th>Gender</th><td>' . ucfirst($student['gender']) . '</td></tr>';
            $html .= '</tbody></table>';
            $html .= '</div>';
            break;
            
        case 'subjects_table':
            $html .= '<div class="canvas-block">';
            $html .= '<div class="canvas-block-title">Academic Performance</div>';
            $html .= '<table class="table table-condensed table-bordered" style="margin-top: 10px;">';
            $html .= '<thead><tr><th>Subject</th>';
            $markDistribution = json_decode($getExam['mark_distribution'], true);
            foreach ($markDistribution as $id) {
                $html .= '<th>' . get_type_name_by_id('exam_mark_distribution', $id) . '</th>';
            }
            $html .= '<th>Total</th><th>Grade</th></tr></thead>';
            $html .= '<tbody>';
            
            $total_grade_point = 0;
            $grand_obtain_marks = 0;
            $grand_full_marks = 0;
            
            foreach ($getMarksList as $row) {
                $total_obtain_marks = 0;
                $total_full_marks = 0;
                $fullMarkDistribution = json_decode($row['mark_distribution'], true);
                $obtainedMark = json_decode($row['get_mark'], true);
                
                $html .= '<tr><td>' . $row['subject_name'] . '</td>';
                
                foreach ($fullMarkDistribution as $i => $val) {
                    $obtained_mark = floatval($obtainedMark[$i]);
                    $fullMark = floatval($val['full_mark']);
                    $total_obtain_marks += $obtained_mark;
                    $total_full_marks += $fullMark;
                    
                    $html .= '<td>';
                    if ($row['get_abs'] == 'on') {
                        $html .= 'Absent';
                    } else {
                        $html .= $obtained_mark . '/' . $fullMark;
                    }
                    $html .= '</td>';
                }
                
                $grand_obtain_marks += $total_obtain_marks;
                $grand_full_marks += $total_full_marks;
                
                $html .= '<td>' . $total_obtain_marks . '/' . $total_full_marks . '</td>';
                
                $percentage_grade = ($total_full_marks > 0) ? ($total_obtain_marks * 100) / $total_full_marks : 0;
                $grade = get_column('grade', 'name', array('branch_id' => $getExam['branch_id'], 'lower_mark <=' => $percentage_grade, 'upper_mark >=' => $percentage_grade));
                $html .= '<td>' . ($grade ? $grade : 'N/A') . '</td>';
                $html .= '</tr>';
            }
            
            $html .= '</tbody></table>';
            $html .= '</div>';
            break;
            
        case 'image_logo':
            $html .= '<div class="canvas-block text-center">';
            $html .= '<img src="' . base_url('uploads/app_image/report-card-logo.png') . '" style="max-width:200px; max-height:100px;" />';
            $html .= '</div>';
            break;
            
        case 'custom_html':
            $html .= '<div class="canvas-block">';
            $html .= '[Custom HTML Block]';
            $html .= '</div>';
            break;
            
        default:
            $html .= '<!-- Unknown block type: ' . $block_type . ' -->';
    }
    
    return $html;
}

// Render each student's report
foreach ($student_array as $sc => $student_id) {
    $result = $this->exam_model->getStudentReportCard($student_id, $examID, $sessionID);
    $student = $result['student'];
    $getMarksList = $result['exam'];
    $rankLabel = isset($student_ranks[$student_id]) ? $student_ranks[$student_id] : 'N/A';
    
    // Front page from canvas layout
    if (!empty($canvas_layouts['front_canvas_layout'])) {
        $frontLayout = $canvas_layouts['front_canvas_layout'];
        ?>
        <div class="mark-container" style="page-break-after: always;">
            <h3 style="text-align: center; margin-bottom: 20px;">Front Page</h3>
            <?php foreach ($frontLayout as $block):
                $block_type = isset($block['content']) ? 'auto-detect' : 'unknown';
                // Try to extract block type from HTML data-block-type attribute or from context
                if (preg_match('/data-block-type=["\']([^"\']+)["\']/', $block['content'], $matches)) {
                    $block_type = $matches[1];
                }
                
                if ($block_type !== 'auto-detect') {
                    echo render_canvas_block($block_type, $student, $student_id, $getMarksList, $getExam, $getSchool, $schoolYear, $student_ranks, $rankLabel);
                } else {
                    echo '<div class="canvas-block">' . ($block['content'] ? substr($block['content'], 0, 200) : '[Block]') . '</div>';
                }
            endforeach; ?>
        </div>
        <?php
    }
    
    // Back page from canvas layout
    if (!empty($canvas_layouts['back_canvas_layout'])) {
        $backLayout = $canvas_layouts['back_canvas_layout'];
        ?>
        <div class="mark-container" style="page-break-after: <?php echo ($sc < count($student_array) - 1) ? 'always' : 'auto'; ?>;">
            <h3 style="text-align: center; margin-bottom: 20px;">Back Page</h3>
            <?php foreach ($backLayout as $block):
                $block_type = isset($block['content']) ? 'auto-detect' : 'unknown';
                if (preg_match('/data-block-type=["\']([^"\']+)["\']/', $block['content'], $matches)) {
                    $block_type = $matches[1];
                }
                
                if ($block_type !== 'auto-detect') {
                    echo render_canvas_block($block_type, $student, $student_id, $getMarksList, $getExam, $getSchool, $schoolYear, $student_ranks, $rankLabel);
                } else {
                    echo '<div class="canvas-block">' . ($block['content'] ? $block['content'] : '[Content Block]') . '</div>';
                }
            endforeach; ?>
        </div>
        <?php
    }
}
?>
