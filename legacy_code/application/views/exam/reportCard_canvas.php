<?php
/**
 * Fabric.js Canvas-based Report Card Renderer
 * Renders absolute pixel-positioned JSON layouts (Semester, Period, End of Year)
 */

if (empty($student_array)) {
    return;
}

$getExam = $this->db->where(array('id' => $examID))->get('exam')->row_array();
$getSchool = $this->db->where(array('id' => $getExam['branch_id']))->get('branch')->row_array();
$schoolYear = get_type_name_by_id('schoolyear', $sessionID, 'school_year');

$templateType = isset($template_type) ? $template_type : 'semester';

// Parse stored design layouts
$rawDesign = isset($canvas_layouts['front_canvas_layout']) ? $canvas_layouts['front_canvas_layout'] : array();
if (is_string($rawDesign)) {
    $rawDesign = json_decode($rawDesign, true);
}

$frontLayout = array();
$backLayout = array();

if (isset($rawDesign['front']) || isset($rawDesign['back'])) {
    if (isset($rawDesign['front'][$templateType])) {
        $frontLayout = $rawDesign['front'][$templateType];
    } elseif (isset($rawDesign['front']) && is_array($rawDesign['front']) && isset($rawDesign['front']['objects'])) {
        $frontLayout = $rawDesign['front'];
    }
    
    if (isset($rawDesign['back'][$templateType])) {
        $backLayout = $rawDesign['back'][$templateType];
    } elseif (isset($rawDesign['back']) && is_array($rawDesign['back']) && isset($rawDesign['back']['objects'])) {
        $backLayout = $rawDesign['back'];
    }
} else {
    $frontLayout = $rawDesign;
}

function render_fabric_objects($layoutData, $student, $getMarksList, $getExam, $getSchool, $schoolYear, $rankLabel, $templateType) {
    if (empty($layoutData) || !isset($layoutData['objects']) || !is_array($layoutData['objects'])) {
        return '';
    }

    $html = '<div style="position: relative; width: 794px; height: 1123px; margin: 0 auto; background: #fff; border: 1px solid #e5e7eb; overflow: hidden; page-break-inside: avoid;">';

    foreach ($layoutData['objects'] as $obj) {
        $left = isset($obj['left']) ? floatval($obj['left']) : 0;
        $top = isset($obj['top']) ? floatval($obj['top']) : 0;
        $width = isset($obj['width']) ? floatval($obj['width']) * (isset($obj['scaleX']) ? floatval($obj['scaleX']) : 1) : 0;
        $height = isset($obj['height']) ? floatval($obj['height']) * (isset($obj['scaleY']) ? floatval($obj['scaleY']) : 1) : 0;

        if (isset($obj['originX']) && $obj['originX'] === 'center') {
            $left -= ($width / 2);
        }
        if (isset($obj['originY']) && $obj['originY'] === 'center') {
            $top -= ($height / 2);
        }

        $type = isset($obj['type']) ? $obj['type'] : '';
        $customType = isset($obj['customType']) ? $obj['customType'] : '';

        if ($type === 'i-text' || $type === 'text') {
            $text = isset($obj['text']) ? $obj['text'] : '';

            // Dynamic Placeholders
            $text = str_replace('[Student Name]', $student['first_name'] . ' ' . $student['last_name'], $text);
            $text = str_replace('[Class & Section]', $student['class_name'] . ' (' . $student['section_name'] . ')', $text);
            $text = str_replace('[Roll Number]', $student['roll'], $text);
            $text = str_replace('[Register No]', $student['register_no'], $text);
            $text = str_replace('[Father Name]', $student['father_name'], $text);
            $text = str_replace('[Academic Session]', $schoolYear, $text);
            $text = str_replace('[Exam Title]', $getExam['name'], $text);
            $text = str_replace('[Rank]', $rankLabel, $text);
            
            $next_class_name = 'N/A';
            if (isset($student['class_id'])) {
                $classObj = $this->db->select('next_class_id')->where('id', $student['class_id'])->get('class')->row();
                if ($classObj && !empty($classObj->next_class_id)) {
                    $next_class_name = get_type_name_by_id('class', $classObj->next_class_id);
                }
            }
            $text = str_replace('[Promoted To Class]', $next_class_name, $text);

            $fontFamily = isset($obj['fontFamily']) ? $obj['fontFamily'] : 'Arial';
            $fontSize = isset($obj['fontSize']) ? intval($obj['fontSize']) : 16;
            $fill = isset($obj['fill']) ? $obj['fill'] : '#000000';
            $fontWeight = (isset($obj['fontWeight']) && $obj['fontWeight'] === 'bold') ? 'bold' : 'normal';
            $fontStyle = (isset($obj['fontStyle']) && $obj['fontStyle'] === 'italic') ? 'italic' : 'normal';
            $textDecoration = (isset($obj['underline']) && $obj['underline']) ? 'underline' : 'none';
            $textAlign = isset($obj['textAlign']) ? $obj['textAlign'] : 'left';

            $style = "position: absolute; left: {$left}px; top: {$top}px; font-family: {$fontFamily}; font-size: {$fontSize}px; color: {$fill}; font-weight: {$fontWeight}; font-style: {$fontStyle}; text-decoration: {$textDecoration}; text-align: {$textAlign}; line-height: 1.2; white-space: pre-wrap;";

            $html .= '<div style="' . $style . '">' . htmlspecialchars($text) . '</div>';
        } elseif ($type === 'image') {
            $src = isset($obj['src']) ? $obj['src'] : base_url('uploads/app_image/report-card-logo.png');
            $style = "position: absolute; left: {$left}px; top: {$top}px; width: {$width}px; height: {$height}px; object-fit: contain;";
            $html .= '<img src="' . htmlspecialchars($src) . '" style="' . $style . '" />';
        } elseif ($customType === 'grades_table' || (isset($obj['objects']) && $customType === 'grades_table')) {
            $style = "position: absolute; left: {$left}px; top: {$top}px; width: {$width}px; min-height: {$height}px;";
            $html .= '<div style="' . $style . '">';
            $html .= '<table class="table table-bordered table-condensed" style="width:100%; border-collapse:collapse; font-size:12px; background:#fff;">';
            $html .= '<thead><tr style="background:#f8fafc;"><th>Subject</th>';

            $markDistribution = json_decode($getExam['mark_distribution'], true);
            if (is_array($markDistribution)) {
                foreach ($markDistribution as $id) {
                    $html .= '<th>' . get_type_name_by_id('exam_mark_distribution', $id) . '</th>';
                }
            }
            $html .= '<th>Total</th><th>Grade</th></tr></thead><tbody>';

            foreach ($getMarksList as $row) {
                $total_obtain_marks = 0;
                $total_full_marks = 0;
                $fullMarkDistribution = json_decode($row['mark_distribution'], true);
                $obtainedMark = json_decode($row['get_mark'], true);

                $html .= '<tr><td>' . htmlspecialchars($row['subject_name']) . '</td>';

                if (is_array($fullMarkDistribution)) {
                    foreach ($fullMarkDistribution as $i => $val) {
                        $obtained_mark = isset($obtainedMark[$i]) ? floatval($obtainedMark[$i]) : 0;
                        $fullMark = floatval($val['full_mark']);
                        $total_obtain_marks += $obtained_mark;
                        $total_full_marks += $fullMark;

                        $html .= '<td>' . ($row['get_abs'] == 'on' ? 'Abs' : $obtained_mark) . '</td>';
                    }
                }

                $html .= '<td>' . $total_obtain_marks . '/' . $total_full_marks . '</td>';
                $percentage_grade = ($total_full_marks > 0) ? ($total_obtain_marks * 100) / $total_full_marks : 0;
                $grade = get_column('grade', 'name', array('branch_id' => $getExam['branch_id'], 'lower_mark <=' => $percentage_grade, 'upper_mark >=' => $percentage_grade));
                $html .= '<td>' . ($grade ? $grade : 'N/A') . '</td>';
                $html .= '</tr>';
            }

            $html .= '</tbody></table></div>';
        }
    }

    $html .= '</div>';
    return $html;
}

foreach ($student_array as $sc => $student_id) {
    $result = $this->exam_model->getStudentReportCard($student_id, $examID, $sessionID);
    $student = $result['student'];
    $getMarksList = $result['exam'];
    $rankLabel = isset($student_ranks[$student_id]) ? $student_ranks[$student_id] : 'N/A';

    // Front Page
    if (!empty($frontLayout)) {
        echo '<div style="margin-bottom: 20px;">';
        echo render_fabric_objects($frontLayout, $student, $getMarksList, $getExam, $getSchool, $schoolYear, $rankLabel, $templateType);
        echo '</div>';
    }

    // Back Page
    if (!empty($backLayout)) {
        echo '<div style="page-break-after: always; margin-bottom: 20px;">';
        echo render_fabric_objects($backLayout, $student, $getMarksList, $getExam, $getSchool, $schoolYear, $rankLabel, $templateType);
        echo '</div>';
    }
}
?>
