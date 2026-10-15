<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Add_report_card_templates extends CI_Migration
{
    public function up()
    {
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ),
            'branch_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false
            ),
            'front_layout_settings' => array(
                'type' => 'TEXT',
                'null' => false
            ),
            'enable_back_page' => array(
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0
            ),
            'back_page_content' => array(
                'type' => 'TEXT',
                'null' => true
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => true
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => true
            ),
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key('branch_id');
        $this->dbforge->create_table('report_card_templates', true);
    }

    public function down()
    {
        $this->dbforge->drop_table('report_card_templates', true);
    }
}
