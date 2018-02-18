<?php

class Migration_update_course_content_5 extends CI_Migration {
    
    public function up() {
        $this->dbforge->add_column('course_content', [
            'creator_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => true,
                'null' => true,
            ],
            'updator_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => true,
                'null' => true,
            ],
        ]);
    }
    
    public function down() {
        $this->dbforge->drop_column('course_content', 'creator_id');
        $this->dbforge->drop_column('course_content', 'updator_id');
    }
    
}
