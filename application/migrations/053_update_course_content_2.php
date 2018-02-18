<?php

class Migration_update_course_content_2 extends CI_Migration {
    
    public function up() {
        $this->dbforge->add_column('course_content', [
            'sorting' => [
                'type' => 'INT',
                'constraint' => 10,
                'null' => FALSE,
                'default' => 0
            ],
        ]);
    }
    
    public function down() {
        $this->dbforge->drop_column('course_content', 'sorting');
    }
    
}
