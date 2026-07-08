<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Report_card_template_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_default_layout()
    {
        return array(
            'show_gpa' => 1,
            'show_class_rank' => 1,
            'show_attendance_summary' => 1,
            'show_vpa_signature_line' => 1,
            'show_principal_signature_line' => 1,
            'grading_scale_legend' => 1,
            'order' => array(
                'student_info',
                'subjects_table',
                'attendance_summary',
                'gpa',
                'class_rank',
                'grade_scale_legend',
                'signature_lines'
            ),
            'titles' => array(
                'student_info' => 'Student Information',
                'subjects_table' => 'Academic Performance',
                'attendance_summary' => 'Attendance Summary',
                'gpa' => 'GPA',
                'class_rank' => 'Class Rank',
                'grade_scale_legend' => 'Grading Scale',
                'signature_lines' => 'Signature Lines'
            ),
        );
    }

    public function get_template($branch_id)
    {
        $template = $this->db->get_where('report_card_templates', array('branch_id' => $branch_id))->row_array();
        if (!$template) {
            return array(
                'id' => 0,
                'branch_id' => $branch_id,
                'front_layout_settings' => json_encode($this->get_default_layout()),
                'enable_back_page' => 0,
                'back_page_content' => '',
                'created_at' => null,
                'updated_at' => null,
            );
        }
        return $template;
    }

    public function save_template($branch_id, $front_settings, $enable_back_page, $back_page_content)
    {
        $data = array(
            'branch_id' => $branch_id,
            'front_layout_settings' => json_encode($front_settings),
            'enable_back_page' => $enable_back_page ? 1 : 0,
            'back_page_content' => $back_page_content,
            'updated_at' => date('Y-m-d H:i:s'),
        );

        $existing = $this->db->get_where('report_card_templates', array('branch_id' => $branch_id))->row_array();
        if ($existing) {
            $this->db->where('id', $existing['id']);
            $this->db->update('report_card_templates', $data);
            return $existing['id'];
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert('report_card_templates', $data);
        return $this->db->insert_id();
    }

    /**
     * Save canvas layouts (GridStack JSON)
     * @param $branch_id int
     * @param $front_canvas_layout array GridStack layout for front page
     * @param $back_canvas_layout array GridStack layout for back page
     */
    public function save_canvas_layouts($branch_id, $front_canvas_layout = null, $back_canvas_layout = null)
    {
        $data = array(
            'updated_at' => date('Y-m-d H:i:s'),
        );

        if ($front_canvas_layout !== null) {
            $data['front_canvas_layout'] = is_array($front_canvas_layout) ? json_encode($front_canvas_layout) : $front_canvas_layout;
        }

        if ($back_canvas_layout !== null) {
            $data['back_canvas_layout'] = is_array($back_canvas_layout) ? json_encode($back_canvas_layout) : $back_canvas_layout;
        }

        $existing = $this->db->get_where('report_card_templates', array('branch_id' => $branch_id))->row_array();
        if ($existing) {
            $this->db->where('id', $existing['id']);
            $this->db->update('report_card_templates', $data);
            return $existing['id'];
        }

        return false;
    }

    /**
     * Get canvas layouts (GridStack JSON)
     * @param $branch_id int
     * @return array with front_canvas_layout and back_canvas_layout
     */
    public function get_canvas_layouts($branch_id)
    {
        $template = $this->db->get_where('report_card_templates', array('branch_id' => $branch_id))->row_array();
        return array(
            'front_canvas_layout' => isset($template['front_canvas_layout']) && $template['front_canvas_layout'] ? json_decode($template['front_canvas_layout'], true) : array(),
            'back_canvas_layout' => isset($template['back_canvas_layout']) && $template['back_canvas_layout'] ? json_decode($template['back_canvas_layout'], true) : array(),
        );
    }

    /**
     * Save GrapeJS design (WYSIWYG HTML editor)
     * @param $branch_id int
     * @param $design array with 'front' and 'back' keys containing html
     * @param $orientation string 'portrait' or 'landscape'
     */
    public function save_grapejs_design($branch_id, $design, $orientation = 'portrait')
    {
        $data = array(
            'front_canvas_layout' => isset($design['front']) ? json_encode($design['front']) : null,
            'back_canvas_layout' => isset($design['back']) ? json_encode($design['back']) : null,
            'updated_at' => date('Y-m-d H:i:s'),
        );

        // Store orientation in front_layout_settings
        $existing = $this->db->get_where('report_card_templates', array('branch_id' => $branch_id))->row_array();
        $front_settings = array();
        if ($existing && !empty($existing['front_layout_settings'])) {
            $front_settings = json_decode($existing['front_layout_settings'], true);
        }
        $front_settings['orientation'] = $orientation;
        $data['front_layout_settings'] = json_encode($front_settings);

        if ($existing) {
            $this->db->where('id', $existing['id']);
            $this->db->update('report_card_templates', $data);
            return $existing['id'];
        }

        $data['branch_id'] = $branch_id;
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert('report_card_templates', $data);
        return $this->db->insert_id();
    }

    /**
     * Get GrapeJS design
     * @param $branch_id int
     * @return array with design data and orientation
     */
    public function get_grapejs_design($branch_id)
    {
        $template = $this->db->get_where('report_card_templates', array('branch_id' => $branch_id))->row_array();
        
        $front_settings = array();
        if ($template && !empty($template['front_layout_settings'])) {
            $front_settings = json_decode($template['front_layout_settings'], true);
        }

        return array(
            'front' => isset($template['front_canvas_layout']) && $template['front_canvas_layout'] ? json_decode($template['front_canvas_layout'], true) : array(),
            'back' => isset($template['back_canvas_layout']) && $template['back_canvas_layout'] ? json_decode($template['back_canvas_layout'], true) : array(),
            'orientation' => isset($front_settings['orientation']) ? $front_settings['orientation'] : 'portrait',
        );
    }
}
