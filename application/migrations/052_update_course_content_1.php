<?php

class Migration_update_course_content_1 extends CI_Migration {
    
    public function up() {
        $this->dbforge->drop_column('course_content', 'permissions');
        
        $this->dbforge->add_column('course_content', [
            'published_from' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE,
            ],
            'published_to' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE,
            ],
            'public' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => FALSE,
                'default' => 0
            ],
        ]);
    }
    
    public function down() {
        $this->dbforge->add_column('course_content', [
            'permissions' => [
                'type' => 'TEXT',
                'null' => TRUE,
            ],
        ]);
    
        $this->dbforge->drop_column('course_content', 'published_from');
        $this->dbforge->drop_column('course_content', 'published_to');
        $this->dbforge->drop_column('course_content', 'public');
    }
    
}
