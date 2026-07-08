<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Migration: Add canvas layout columns for visual block editor
 * Stores front and back page canvas layouts as JSON (GridStack format)
 */
class Migration_Add_canvas_layouts_to_report_card_templates extends CI_Migration
{
    public function up()
    {
        // Add front_canvas_layout column if it doesn't exist
        $this->db->query("ALTER TABLE report_card_templates ADD COLUMN front_canvas_layout LONGTEXT NULL AFTER front_layout_settings");
        
        // Add back_canvas_layout column if it doesn't exist
        $this->db->query("ALTER TABLE report_card_templates ADD COLUMN back_canvas_layout LONGTEXT NULL AFTER back_page_content");
    }

    public function down()
    {
        // Drop the columns if rolling back
        if ($this->db->field_exists('front_canvas_layout', 'report_card_templates')) {
            $this->db->query("ALTER TABLE report_card_templates DROP COLUMN front_canvas_layout");
        }
        
        if ($this->db->field_exists('back_canvas_layout', 'report_card_templates')) {
            $this->db->query("ALTER TABLE report_card_templates DROP COLUMN back_canvas_layout");
        }
    }
}
