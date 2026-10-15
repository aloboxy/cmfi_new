<style type="text/css">
    @media print {
        .page-break {
            page-break-after: always;
        }
        .report-page.landscape {
            page-break-after: always;
        }
    }
    .report-page {
        width: 100%;
        min-height: 11in;
        margin: 0 auto 20px;
        padding: 40px;
        background: white;
        box-sizing: border-box;
    }
    .report-page.landscape {
        min-height: 8.5in;
    }
    .mark-container {
        background: #fff;
        width: 1000px;
        position: relative;
        z-index: 2;
        margin: 0 auto;
        padding: 20px 30px;
    }
    table {
        border-collapse: collapse;
        width: 100%;
        margin: 0 auto;
    }
</style>

<?php
$template = isset($template) ? $template : array();
$grapejs_design = isset($grapejs_design) ? $grapejs_design : array();
$canvas_layouts = isset($canvas_layouts) ? $canvas_layouts : array();

// Priority: GrapeJS > Canvas Layouts > Legacy Sequential Layout

// Check if GrapeJS design exists
if (!empty($grapejs_design['front']) || !empty($grapejs_design['back'])) {
    $orientation = isset($grapejs_design['orientation']) ? $grapejs_design['orientation'] : 'portrait';
    $orientationClass = ($orientation === 'landscape') ? 'landscape' : '';
    
    if (count($student_array)) {
        foreach ($student_array as $sc => $studentID) {
            $result = $this->exam_model->getStudentReportCard($studentID, $examID, $sessionID);
            ?>
            <!-- Front Page (GrapeJS Design) -->
            <div class="report-page <?= $orientationClass; ?>">
                <?= isset($grapejs_design['front']) && is_array($grapejs_design['front']) && isset($grapejs_design['front']['html']) ? $grapejs_design['front']['html'] : ($grapejs_design['front'] ?? '') ?>
            </div>
            
            <!-- Back Page (GrapeJS Design) -->
            <?php if (!empty($grapejs_design['back'])): ?>
            <div class="report-page <?= $orientationClass; ?>">
                <?= isset($grapejs_design['back']) && is_array($grapejs_design['back']) && isset($grapejs_design['back']['html']) ? $grapejs_design['back']['html'] : ($grapejs_design['back'] ?? '') ?>
            </div>
            <?php endif; ?>
            <?php
        }
    }
    return; // Use GrapeJS design, skip other layouts
}

// Fallback to canvas layouts if GrapeJS not available
$use_canvas_layout = !empty($canvas_layouts['front_canvas_layout']);
if ($use_canvas_layout) {
    echo $this->load->view('exam/reportCard_canvas', array(
        'template' => $template,
        'canvas_layouts' => $canvas_layouts,
        'student_array' => $student_array,
        'remarks_array' => isset($remarks_array) ? $remarks_array : array(),
        'grade_scale' => isset($grade_scale) ? $grade_scale : 0,
        'attendance' => isset($attendance) ? $attendance : 0,
        'print_date' => isset($print_date) ? $print_date : '',
        'examID' => isset($examID) ? $examID : 0,
        'sessionID' => isset($sessionID) ? $sessionID : 0,
        'student_ranks' => isset($student_ranks) ? $student_ranks : array(),
    ), true);
    return; 
}

// Fallback to legacy sequential layout
$front_settings = array();
if (!empty($template['front_layout_settings'])) {
    $front_settings = json_decode($template['front_layout_settings'], true);
}
$front_settings = is_array($front_settings) ? $front_settings : array();
$section_order = isset($front_settings['order']) && is_array($front_settings['order']) ? $front_settings['order'] : array(
    'student_info',
    'subjects_table',
    'attendance_summary',
    'gpa',
    'class_rank',
    'grade_scale_legend',
    'signature_lines'
);
$section_titles = array_merge(array(
    'student_info' => 'Student Information',
    'subjects_table' => 'Academic Performance',
    'attendance_summary' => 'Attendance Summary',
    'gpa' => 'GPA',
    'class_rank' => 'Class Rank',
    'grade_scale_legend' => 'Grading Scale',
    'signature_lines' => 'Signature Lines',
), isset($front_settings['titles']) ? $front_settings['titles'] : array());
$show_gpa = isset($front_settings['show_gpa']) ? $front_settings['show_gpa'] : 1;
$show_class_rank = isset($front_settings['show_class_rank']) ? $front_settings['show_class_rank'] : 0;
$show_attendance_summary = isset($front_settings['show_attendance_summary']) ? $front_settings['show_attendance_summary'] : $attendance;
$show_vpa_signature_line = isset($front_settings['show_vpa_signature_line']) ? $front_settings['show_vpa_signature_line'] : 0;
$show_principal_signature_line = isset($front_settings['show_principal_signature_line']) ? $front_settings['show_principal_signature_line'] : 0;
$show_grade_scale_legend = isset($front_settings['grading_scale_legend']) ? $front_settings['grading_scale_legend'] : $grade_scale;
$enable_back_page = isset($template['enable_back_page']) ? $template['enable_back_page'] : 0;

if (count($student_array)) {
    $getExam = $this->db->where(array('id' => $examID))->get('exam')->row_array();
    $getSchool = $this->db->where(array('id' => $getExam['branch_id']))->get('branch')->row_array();
    $schoolYear = get_type_name_by_id('schoolyear', $sessionID, 'school_year');

    foreach ($student_array as $sc => $studentID) {
        $result = $this->exam_model->getStudentReportCard($studentID, $examID, $sessionID);
        $student = $result['student'];
        $getMarksList = $result['exam'];
        $rankLabel = isset($student_ranks[$studentID]) ? $student_ranks[$studentID] : 'N/A';
        ?>
    <div class="mark-container front-page">
        <?php foreach ($section_order as $section) {
            switch ($section) {
                case 'student_info':
                    ?>
                    <table border="0" style="margin-top: 20px; height: 100px;">
                        <tbody>
                            <tr>
                                <td style="width:40%;vertical-align: top;"><img style="max-width:225px;" src="<?= base_url('uploads/app_image/report-card-logo.png'); ?>"></td>
                                <td style="width:60%;vertical-align: top;">
                                    <table align="right" class="table-head text-right" >
                                        <tbody>
                                            <tr><th style="font-size: 26px;" class="text-right"><?= $getSchool['school_name'] ?></th></tr>
                                            <tr><th style="font-size: 14px; padding-top: 4px;" class="text-right">Academic Session : <?= $schoolYear ?></th></tr>
                                            <tr><td><?= $getSchool['address'] ?></td></tr>
                                            <tr><td><?= $getSchool['mobileno'] ?></td></tr>
                                            <tr><td><?= $getSchool['email'] ?></td></tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table table-bordered" style="margin-top: 20px;">
                        <tbody>
                            <tr>
                                <th>Name</th>
                                <td><?= $student['first_name'] . ' ' . $student['last_name'] ?></td>
                                <th>Register No</th>
                                <td><?= $student['register_no'] ?></td>
                                <th>Roll Number</th>
                                <td><?= $student['roll'] ?></td>
                            </tr>
                            <tr>
                                <th>Father Name</th>
                                <td><?= $student['father_name'] ?></td>
                                <th>Admission Date</th>
                                <td><?= _d($student['admission_date']) ?></td>
                                <th>Date of Birth</th>
                                <td><?= _d($student['birthday']) ?></td>
                            </tr>
                            <tr>
                                <th>Mother Name</th>
                                <td><?= $student['mother_name'] ?></td>
                                <th>Class</th>
                                <td><?= $student['class_name'] . ' (' . $student['section_name'] . ')' ?></td>
                                <th>Gender</th>
                                <td><?= ucfirst($student['gender']) ?></td>
                            </tr>
                            <tr>
                                <th>Promoted To Class</th>
                                <td colspan="5">
                                    <?php 
                                    $next_class_name = 'N/A';
                                    if (isset($student['class_id'])) {
                                        $classObj = $this->db->select('next_class_id')->where('id', $student['class_id'])->get('class')->row();
                                        if ($classObj && !empty($classObj->next_class_id)) {
                                            $next_class_name = get_type_name_by_id('class', $classObj->next_class_id);
                                        }
                                    }
                                    echo $next_class_name;
                                    ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <?php
                    break;
                case 'subjects_table':
                    ?>
                    <table class="table table-condensed table-bordered mt-lg">
                        <thead>
                            <tr>
                                <th><?= $section_titles['subjects_table'] ?></th>
                                <?php
                                $markDistribution = json_decode($getExam['mark_distribution'], true);
                                foreach ($markDistribution as $id) {
                                    ?>
                                    <th><?= get_type_name_by_id('exam_mark_distribution', $id) ?></th>
                                <?php } ?>
                                <?php if ($getExam['type_id'] == 1) { ?>
                                    <th>Total</th>
                                <?php } elseif ($getExam['type_id'] == 2) { ?>
                                    <th>Grade</th>
                                    <th>Point</th>
                                <?php } elseif ($getExam['type_id'] == 3) { ?>
                                    <th>Total</th>
                                    <th>Grade</th>
                                    <th>Point</th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $colspan = count($markDistribution) + 1;
                            $total_grade_point = 0;
                            $grand_obtain_marks = 0;
                            $grand_full_marks = 0;
                            $result_status = 1;
                            foreach ($getMarksList as $row) {
                                $total_obtain_marks = 0;
                                $total_full_marks = 0;
                                $fullMarkDistribution = json_decode($row['mark_distribution'], true);
                                $obtainedMark = json_decode($row['get_mark'], true);
                                ?>
                                <tr>
                                    <td valign="middle" width="35%"><?= $row['subject_name'] ?></td>
                                    <?php
                                    foreach ($fullMarkDistribution as $i => $val) {
                                        $obtained_mark = floatval($obtainedMark[$i]);
                                        $fullMark = floatval($val['full_mark']);
                                        $passMark = floatval($val['pass_mark']);
                                        if ($obtained_mark < $passMark) {
                                            $result_status = 0;
                                        }

                                        $total_obtain_marks += $obtained_mark;
                                        $obtained = $row['get_abs'] == 'on' ? 'Absent' : $obtained_mark;
                                        $total_full_marks += $fullMark;
                                        if ($getExam['type_id'] == 1 || $getExam['type_id'] == 3) {
                                            ?>
                                            <td valign="middle">
                                                <?php
                                                if ($row['get_abs'] == 'on') {
                                                    echo 'Absent';
                                                } else {
                                                    echo $obtained_mark . '/' . $fullMark;
                                                }
                                                ?>
                                            </td>
                                            <?php
                                        }
                                        if ($getExam['type_id'] == 2) {
                                            ?>
                                            <td valign="middle">
                                                <?php
                                                if ($row['get_abs'] == 'on') {
                                                    echo 'Absent';
                                                } else {
                                                    $percentage_grade = ($obtained_mark * 100) / $fullMark;
                                                    $grade = $this->exam_model->get_grade($percentage_grade, $getExam['branch_id']);
                                                    echo $grade['name'];
                                                }
                                                ?>
                                            </td>
                                            <?php
                                        }
                                    }
                                    $grand_obtain_marks += $total_obtain_marks;
                                    $grand_full_marks += $total_full_marks;
                                    if ($getExam['type_id'] == 1 || $getExam['type_id'] == 3) {
                                        ?>
                                        <td valign="middle"><?= $total_obtain_marks . '/' . $total_full_marks ?></td>
                                        <?php
                                    }
                                    if ($getExam['type_id'] == 2) {
                                        $percentage_grade = ($total_obtain_marks * 100) / $total_full_marks;
                                        $grade = $this->exam_model->get_grade($percentage_grade, $getExam['branch_id']);
                                        $total_grade_point += $grade['grade_point'];
                                        ?>
                                        <td valign="middle"><?= $grade['name'] ?></td>
                                        <td valign="middle"><?= number_format($grade['grade_point'], 2, '.', '') ?></td>
                                        <?php
                                    }
                                    if ($getExam['type_id'] == 3) {
                                        $colspan += 2;
                                        $percentage_grade = ($total_obtain_marks * 100) / $total_full_marks;
                                        $grade = $this->exam_model->get_grade($percentage_grade, $getExam['branch_id']);
                                        $total_grade_point += $grade['grade_point'];
                                        ?>
                                        <td valign="middle"><?= $grade['name'] ?></td>
                                        <td valign="middle"><?= number_format($grade['grade_point'], 2, '.', '') ?></td>
                                        <?php
                                    }
                                    ?>
                                </tr>
                            <?php } ?>

                            <?php if ($getExam['type_id'] == 1 || $getExam['type_id'] == 3) { ?>
                                <tr class="text-weight-semibold">
                                    <td valign="top">GRAND TOTAL :</td>
                                    <td valign="top" colspan="<?= $colspan ?>"><?= $grand_obtain_marks . '/' . $grand_full_marks; ?>, Average : <?php $percentage = ($grand_obtain_marks * 100) / $grand_full_marks; echo number_format($percentage, 2, '.', '') ?></td>
                                </tr>
                                <tr class="text-weight-semibold">
                                    <td valign="top">GRAND TOTAL IN WORDS :</td>
                                    <td valign="top" colspan="<?= $colspan ?>">
                                        <?php
                                        $f = new NumberFormatter('en', NumberFormatter::SPELLOUT);
                                        echo ucwords($f->format($grand_obtain_marks));
                                        ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            <?php if ($getExam['type_id'] == 2) { ?>
                                <tr class="text-weight-semibold">
                                    <td valign="top">GPA :</td>
                                    <td valign="top" colspan="<?= $colspan + 1 ?>"><?= number_format(($total_grade_point / count($getMarksList)), 2, '.', '') ?></td>
                                </tr>
                            <?php } ?>
                            <?php if ($getExam['type_id'] == 3) { ?>
                                <tr class="text-weight-semibold">
                                    <td valign="top">GPA :</td>
                                    <td valign="top" colspan="<?= $colspan ?>"><?= number_format(($total_grade_point / count($getMarksList)), 2, '.', '') ?></td>
                                </tr>
                            <?php } ?>
                            <?php if ($getExam['type_id'] == 1 || $getExam['type_id'] == 3) { ?>
                                <tr class="text-weight-semibold">
                                    <td valign="top">RESULT :</td>
                                    <td valign="top" colspan="<?= $colspan ?>"><?= $result_status == 0 ? 'Fail' : 'Pass'; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <?php
                    break;
                case 'attendance_summary':
                    if ($show_attendance_summary) {
                        $year = explode('-', $schoolYear);
                        $getTotalWorking = $this->db->where(array('student_id' => $studentID, 'status !=' => 'H', 'year(date)' => $year[0]))->get('student_attendance')->num_rows();
                        $getTotalAttendance = $this->db->where(array('student_id' => $studentID, 'status' => 'P', 'year(date)' => $year[0]))->get('student_attendance')->num_rows();
                        $attenPercentage = empty($getTotalWorking) ? 0 : ($getTotalAttendance * 100) / $getTotalWorking;
                        ?>
                        <div style="width: 100%; display: flex; margin-top: 15px;">
                            <div style="width: 50%; padding-right: 15px;">
                                <table class="table table-bordered table-condensed">
                                    <tbody>
                                        <tr>
                                            <th colspan="2" class="text-center"><?= $section_titles['attendance_summary'] ?></th>
                                        </tr>
                                        <tr>
                                            <th style="width: 65%;">No. of working days</th>
                                            <td><?= $getTotalWorking ?></td>
                                        </tr>
                                        <tr>
                                            <th style="width: 65%;">No. of days attended</th>
                                            <td><?= $getTotalAttendance ?></td>
                                        </tr>
                                        <tr>
                                            <th style="width: 65%;">Attendance Percentage</th>
                                            <td><?= number_format($attenPercentage, 2, '.', '') ?>%</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php
                    }
                    break;
                case 'gpa':
                    if ($show_gpa) {
                        ?>
                        <div style="width: 100%; margin-top: 15px;">
                            <table class="table table-condensed table-bordered">
                                <tbody>
                                    <tr>
                                        <th style="width: 35%;"><?= $section_titles['gpa'] ?></th>
                                        <td>
                                            <?php
                                            if ($getExam['type_id'] == 2 || $getExam['type_id'] == 3) {
                                                echo number_format(($total_grade_point / count($getMarksList)), 2, '.', '');
                                            } else {
                                                echo 'N/A';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <?php
                    }
                    break;
                case 'class_rank':
                    if ($show_class_rank) {
                        ?>
                        <div style="width: 100%; margin-top: 15px;">
                            <table class="table table-condensed table-bordered">
                                <tbody>
                                    <tr>
                                        <th style="width: 35%;"><?= $section_titles['class_rank'] ?></th>
                                        <td><?= $rankLabel ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <?php
                    }
                    break;
                case 'grade_scale_legend':
                    if ($show_grade_scale_legend && $getExam['type_id'] != 1) {
                        ?>
                        <div style="width: 100%; margin-top: 15px;">
                            <table class="table table-condensed table-bordered">
                                <tbody>
                                    <tr>
                                        <th colspan="3" class="text-center"><?= $section_titles['grade_scale_legend'] ?></th>
                                    </tr>
                                    <tr>
                                        <th>Grade</th>
                                        <th>Min Percentage</th>
                                        <th>Max Percentage</th>
                                    </tr>
                                    <?php
                                    $grade = $this->db->where('branch_id', $getExam['branch_id'])->get('grade')->result_array();
                                    foreach ($grade as $row) {
                                        ?>
                                        <tr>
                                            <td style="width: 30%;"><?= $row['name'] ?></td>
                                            <td style="width: 30%;"><?= $row['lower_mark'] ?>%</td>
                                            <td style="width: 30%;"><?= $row['upper_mark'] ?>%</td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <?php
                    }
                    break;
                case 'signature_lines':
                    ?>
                    <div style="width:100%; outline:none; margin-top: 35px;">
                        <table style="width:100%; outline:none;">
                            <tbody>
                                <tr>
                                    <?php if ($show_principal_signature_line) { ?>
                                        <td style="border-top: 1px solid #ddd; font-size:15px; text-align:left;">Principal Signature</td>
                                    <?php } ?>
                                    <td style="border-top: 1px solid #ddd; font-size:15px; text-align:center;">Class Teacher Signature</td>
                                    <?php if ($show_vpa_signature_line) { ?>
                                        <td style="border-top: 1px solid #ddd; font-size:15px; text-align:right;">VPA Signature</td>
                                    <?php } ?>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <?php
                    break;
            }
        } ?>

        <div style="width: 100%; margin-top: 15px;">
            <table class="table table-condensed table-bordered">
                <tbody>
                    <tr>
                        <th style="width: 250px;">Print Date</th>
                        <td><?= _d($print_date) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <?php if ($enable_back_page) { ?>
        <div class="page-break"></div>
        <div class="mark-container back-page">
            <h4 style="margin-bottom: 20px;">Back Page</h4>
            <div style="line-height: 1.6;">
                <?= $template['back_page_content'] ?>
            </div>
        </div>
        <?php if ($sc < count($student_array) - 1) { ?>
            <div class="page-break"></div>
        <?php } ?>
    <?php } else { ?>
        <?php if ($sc < count($student_array) - 1) { ?>
            <div class="page-break"></div>
        <?php } ?>
    <?php } ?>

<?php }
}
