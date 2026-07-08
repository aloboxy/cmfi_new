<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Grades_model — Liberian Grading System
 * Handles grade_sheet, academic_period, academic_semester, grade_weight_config
 */
class Grades_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // ─── Periods ────────────────────────────────────────────────────────────

    public function getPeriods($branch_id = null, $session_id = null)
    {
        $branch_id  = $branch_id  ?: get_loggedin_branch_id();
        $session_id = $session_id ?: get_session_id();
        return $this->db->where('branch_id', $branch_id)
                        ->where('session_id', $session_id)
                        ->order_by('sort_order', 'ASC')
                        ->get('academic_period')->result_array();
    }

    public function getPeriod($id)
    {
        return $this->db->where('id', $id)->get('academic_period')->row_array();
    }

    public function savePeriod($data, $id = null)
    {
        $row = [
            'name'               => $data['name'],
            'semester_id'        => $data['semester_id'] ?: null,
            'start_date'         => $data['start_date'],
            'end_date'           => $data['end_date'],
            'lock_date'          => !empty($data['lock_date']) ? $data['lock_date'] : null,
            'lock_interval_days' => (int)($data['lock_interval_days'] ?? 0),
            'sort_order'         => (int)($data['sort_order'] ?? 1),
            'branch_id'          => get_loggedin_branch_id(),
            'session_id'         => get_session_id(),
        ];
        if ($id) {
            $this->db->where('id', $id)->update('academic_period', $row);
        } else {
            $this->db->insert('academic_period', $row);
        }
    }

    public function deletePeriod($id)
    {
        $this->db->where('id', $id)->delete('academic_period');
    }

    // ─── Semesters ───────────────────────────────────────────────────────────

    public function getSemesters($branch_id = null, $session_id = null)
    {
        $branch_id  = $branch_id  ?: get_loggedin_branch_id();
        $session_id = $session_id ?: get_session_id();
        return $this->db->where('branch_id', $branch_id)
                        ->where('session_id', $session_id)
                        ->get('academic_semester')->result_array();
    }

    public function saveSemester($data, $id = null)
    {
        $row = [
            'name'       => $data['name'],
            'branch_id'  => get_loggedin_branch_id(),
            'session_id' => get_session_id(),
        ];
        if ($id) {
            $this->db->where('id', $id)->update('academic_semester', $row);
        } else {
            $this->db->insert('academic_semester', $row);
        }
    }

    public function deleteSemester($id)
    {
        $this->db->where('id', $id)->delete('academic_semester');
    }

    // ─── Grade Weight Config ─────────────────────────────────────────────────

    public function getWeightConfig($branch_id = null)
    {
        $branch_id = $branch_id ?: get_loggedin_branch_id();
        return $this->db->where('branch_id', $branch_id)
                        ->order_by('sort_order', 'ASC')
                        ->get('grade_weight_config')->result_array();
    }

    public function saveWeightConfig($components, $branch_id = null)
    {
        $branch_id = $branch_id ?: get_loggedin_branch_id();
        // Delete existing config for branch and re-insert
        $this->db->where('branch_id', $branch_id)->delete('grade_weight_config');
        $order = 1;
        foreach ($components as $comp) {
            if (empty($comp['component'])) continue;
            $this->db->insert('grade_weight_config', [
                'branch_id'      => $branch_id,
                'component'      => $this->security->xss_clean($comp['component']),
                'label'          => $this->security->xss_clean($comp['label']),
                'max_score'      => (float)$comp['max_score'],
                'weight_percent' => (float)$comp['weight_percent'],
                'is_active'      => isset($comp['is_active']) ? 1 : 0,
                'sort_order'     => $order++,
            ]);
        }
    }

    // ─── Period Lock Check ───────────────────────────────────────────────────

    /**
     * Returns true if grade entry window is currently CLOSED for this period.
     */
    public function isPeriodLocked($period_id)
    {
        $period = $this->getPeriod($period_id);
        if (empty($period)) return false;
        $today = date('Y-m-d');

        // Priority 1: explicit lock_date
        if (!empty($period['lock_date'])) {
            return $today > $period['lock_date'];
        }
        // Priority 2: lock_interval_days after end_date
        if (!empty($period['end_date']) && !empty($period['lock_interval_days'])) {
            $lockDate = date('Y-m-d', strtotime($period['end_date'] . ' +' . (int)$period['lock_interval_days'] . ' days'));
            return $today > $lockDate;
        }
        return false;
    }

    // ─── Grade Sheet Entries ─────────────────────────────────────────────────

    public function getGradeSheet($student_id, $subject_id, $period_id, $session_id, $branch_id)
    {
        return $this->db->where([
            'student_id' => $student_id,
            'subject_id' => $subject_id,
            'period_id'  => $period_id,
            'session_id' => $session_id,
            'branch_id'  => $branch_id,
        ])->get('grade_sheet')->row_array();
    }

    /**
     * Calculate weighted total from component scores and branch config.
     */
    public function calcTotal($scores, $branch_id)
    {
        $weights = $this->getWeightConfig($branch_id);
        $total   = 0;
        foreach ($weights as $w) {
            if (!$w['is_active']) continue;
            $key   = $w['component'] . '_score';
            $score = isset($scores[$key]) ? (float)$scores[$key] : 0;
            $max   = (float)$w['max_score'];
            $pct   = (float)$w['weight_percent'];
            // Clamp score to max
            $score = min($score, $max);
            // Weighted contribution
            $total += ($max > 0) ? ($score / $max) * $pct : 0;
        }
        return round($total, 2);
    }

    public function saveGradeSheet($data)
    {
        $branch_id = (int)$data['branch_id'];
        $scores = [
            'attendance_score'    => (float)($data['attendance_score']    ?? 0),
            'participation_score' => (float)($data['participation_score'] ?? 0),
            'quiz_score'          => (float)($data['quiz_score']          ?? 0),
            'test_score'          => (float)($data['test_score']          ?? 0),
        ];
        $total        = $this->calcTotal($scores, $branch_id);
        $grade_letter = $this->resolveGradeLetter($total, $branch_id);
        $is_absent    = isset($data['is_absent']) ? 1 : 0;

        $row = array_merge($scores, [
            'student_id'   => (int)$data['student_id'],
            'subject_id'   => (int)$data['subject_id'],
            'class_id'     => (int)$data['class_id'],
            'section_id'   => (int)$data['section_id'],
            'period_id'    => (int)$data['period_id'],
            'semester_id'  => !empty($data['semester_id']) ? (int)$data['semester_id'] : null,
            'session_id'   => (int)$data['session_id'],
            'branch_id'    => $branch_id,
            'teacher_id'   => !empty($data['teacher_id']) ? (int)$data['teacher_id'] : null,
            'total_score'  => $total,
            'grade_letter' => $grade_letter,
            'remarks'      => $this->security->xss_clean($data['remarks'] ?? ''),
            'is_absent'    => $is_absent,
            'status'       => 'draft',
        ]);

        // Upsert
        $existing = $this->getGradeSheet(
            $row['student_id'], $row['subject_id'], $row['period_id'],
            $row['session_id'], $row['branch_id']
        );
        if ($existing) {
            $this->db->where('id', $existing['id'])->update('grade_sheet', $row);
            return $existing['id'];
        } else {
            $this->db->insert('grade_sheet', $row);
            return $this->db->insert_id();
        }
    }

    /**
     * Bulk submit a batch of grade_sheet IDs for VPA approval.
     */
    public function submitForApproval($ids)
    {
        if (empty($ids)) return;
        $this->db->where_in('id', $ids)
                 ->where('status', 'draft')
                 ->update('grade_sheet', [
                     'status'       => 'submitted',
                     'submitted_at' => date('Y-m-d H:i:s'),
                 ]);
    }

    // ─── VPA Approval ────────────────────────────────────────────────────────

    /**
     * Get grade sheets pending VPA approval, grouped by class/subject/period.
     */
    public function getPendingApprovals($branch_id = null)
    {
        $branch_id = $branch_id ?: get_loggedin_branch_id();
        $this->db->select('gs.*, c.name as class_name, sec.name as section_name,
                            sub.name as subject_name, ap.name as period_name,
                            CONCAT(st.first_name," ",st.last_name) as student_name,
                            CONCAT(e.first_name," ",e.last_name) as teacher_name,
                            e.id as emp_id');
        $this->db->from('grade_sheet gs');
        $this->db->join('student st', 'st.id = gs.student_id', 'left');
        $this->db->join('subject sub', 'sub.id = gs.subject_id', 'left');
        $this->db->join('class c', 'c.id = gs.class_id', 'left');
        $this->db->join('section sec', 'sec.id = gs.section_id', 'left');
        $this->db->join('academic_period ap', 'ap.id = gs.period_id', 'left');
        $this->db->join('staff e', 'e.id = gs.teacher_id', 'left');
        $this->db->where('gs.branch_id', $branch_id)
                 ->where('gs.status', 'submitted')
                 ->order_by('gs.class_id, gs.subject_id, gs.period_id, st.first_name');
        return $this->db->get()->result_array();
    }

    public function approveGradeSheet($id, $vpa_staff_id, $comment = '')
    {
        $this->db->where('id', $id)->update('grade_sheet', [
            'status'      => 'approved',
            'approved_by' => $vpa_staff_id,
            'approved_at' => date('Y-m-d H:i:s'),
            'vpa_comment' => $this->security->xss_clean($comment),
        ]);
    }

    public function rejectGradeSheet($id, $vpa_staff_id, $comment = '')
    {
        $this->db->where('id', $id)->update('grade_sheet', [
            'status'      => 'rejected',
            'approved_by' => $vpa_staff_id,
            'approved_at' => date('Y-m-d H:i:s'),
            'vpa_comment' => $this->security->xss_clean($comment),
        ]);
    }

    public function bulkApprove($ids, $vpa_staff_id)
    {
        if (empty($ids)) return;
        $this->db->where_in('id', $ids)->update('grade_sheet', [
            'status'      => 'approved',
            'approved_by' => $vpa_staff_id,
            'approved_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function bulkReject($ids, $vpa_staff_id, $comment = '')
    {
        if (empty($ids)) return;
        $this->db->where_in('id', $ids)->update('grade_sheet', [
            'status'      => 'rejected',
            'approved_by' => $vpa_staff_id,
            'approved_at' => date('Y-m-d H:i:s'),
            'vpa_comment' => $this->security->xss_clean($comment),
        ]);
    }

    // ─── Reporting ───────────────────────────────────────────────────────────

    /**
     * Fetch approved grade sheets for report generation.
     * $filter = ['class_id','section_id','period_id','semester_id','year_end','session_id','branch_id']
     */
    public function getApprovedSheets($filter)
    {
        $this->db->select('gs.*, c.name as class_name, sec.name as section_name,
                            sub.name as subject_name, ap.name as period_name, apsem.name as semester_name,
                            CONCAT(st.first_name," ",st.last_name) as student_name,
                            st.register_no, st.photo, st.gender, st.birthday');
        $this->db->from('grade_sheet gs');
        $this->db->join('student st', 'st.id = gs.student_id', 'left');
        $this->db->join('subject sub', 'sub.id = gs.subject_id', 'left');
        $this->db->join('class c', 'c.id = gs.class_id', 'left');
        $this->db->join('section sec', 'sec.id = gs.section_id', 'left');
        $this->db->join('academic_period ap', 'ap.id = gs.period_id', 'left');
        $this->db->join('academic_semester apsem', 'apsem.id = gs.semester_id', 'left');
        $this->db->where('gs.status', 'approved');
        $this->db->where('gs.branch_id', $filter['branch_id']);
        $this->db->where('gs.session_id', $filter['session_id']);

        if (!empty($filter['class_id']))    $this->db->where('gs.class_id', $filter['class_id']);
        if (!empty($filter['section_id'])) $this->db->where('gs.section_id', $filter['section_id']);
        if (!empty($filter['year_end'])) {
            // Year-end: all periods in session — no period filter
        } else {
            if (!empty($filter['period_id']))   $this->db->where('gs.period_id', $filter['period_id']);
            if (!empty($filter['semester_id'])) $this->db->where('gs.semester_id', $filter['semester_id']);
        }
        $this->db->order_by('st.first_name, sub.name, ap.sort_order');
        return $this->db->get()->result_array();
    }

    /**
     * Aggregate year-end averages per student per subject.
     */
    public function getYearEndAverages($class_id, $section_id, $session_id, $branch_id)
    {
        $sql = "SELECT gs.student_id, gs.subject_id,
                       CONCAT(st.first_name,' ',st.last_name) AS student_name,
                       st.register_no, st.photo, st.gender, st.birthday,
                       sub.name AS subject_name,
                       c.name AS class_name, sec.name AS section_name,
                       ROUND(AVG(gs.total_score),2) AS year_avg,
                       COUNT(gs.id) AS period_count
                FROM grade_sheet gs
                JOIN student st ON st.id = gs.student_id
                JOIN subject sub ON sub.id = gs.subject_id
                JOIN class c ON c.id = gs.class_id
                JOIN section sec ON sec.id = gs.section_id
                WHERE gs.status = 'approved'
                  AND gs.class_id   = ?
                  AND gs.section_id = ?
                  AND gs.session_id = ?
                  AND gs.branch_id  = ?
                GROUP BY gs.student_id, gs.subject_id
                ORDER BY st.first_name, sub.name";
        return $this->db->query($sql, [$class_id, $section_id, $session_id, $branch_id])->result_array();
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function resolveGradeLetter($total, $branch_id)
    {
        $grades = $this->db->where('branch_id', $branch_id)->get('grade')->result_array();
        foreach ($grades as $g) {
            if ($total >= $g['lower_mark'] && $total <= $g['upper_mark']) {
                return $g['name'];
            }
        }
        return 'N/A';
    }

    public function getStudentsByClass($class_id, $section_id, $branch_id, $session_id)
    {
        $this->db->select('en.*, CONCAT(st.first_name," ",st.last_name) as student_name,
                            st.register_no, st.photo');
        $this->db->from('enroll en');
        $this->db->join('student st', 'st.id = en.student_id', 'inner');
        $this->db->where('en.class_id', $class_id)
                 ->where('en.section_id', $section_id)
                 ->where('en.branch_id', $branch_id)
                 ->where('en.session_id', $session_id)
                 ->order_by('en.roll', 'ASC');
        return $this->db->get()->result_array();
    }

    public function getSubjectsByClass($class_id, $branch_id, $session_id)
    {
        $this->db->select('s.*');
        $this->db->from('subject s');
        $this->db->join('class_subject cs', 'cs.subject_id = s.id', 'inner');
        $this->db->where('cs.class_id', $class_id)
                 ->where('cs.branch_id', $branch_id)
                 ->where('cs.session_id', $session_id);
        return $this->db->get()->result_array();
    }
}
