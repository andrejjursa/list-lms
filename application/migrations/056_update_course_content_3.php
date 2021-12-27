<?php

class Migration_update_course_content_3 extends CI_Migration
{
    
    public function up()
    {
        $this->dbforge->add_column('course_content', [
            'course_content_group_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'unsigned'   => true,
            ],
        ]);
    }
    
    public function down()
    {
        $this->dbforge->drop_column('course_content', 'course_content_group_id');
    }
    
}
