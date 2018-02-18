<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_update_courses_4 extends CI_Migration {
    
    public function up() {
        $this->dbforge->add_column('courses', [
            'disable_public_groups_page' => [
                'type' => 'tinyint',
                'constraint' => '1',
                'unsigned' => true,
                'default' => 0,
            ],
            'additional_menu_links' => [
                'type' => 'text',
                'null' => true,
            ]
        ]);
    }
    
    public function down() {
        $this->dbforge->drop_column('courses', 'disable_public_groups_page');
        $this->dbforge->drop_column('courses', 'additional_menu_links');
    }
    
}