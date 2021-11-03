<?php

class Migration_update_course_content_4 extends CI_Migration
{
    
    public function up()
    {
        $this->dbforge->add_column('course_content', [
            'files_visibility' => [
                'type' => 'TEXT',
            ],
        ]);
    }
    
    public function down()
    {
        $this->dbforge->drop_column('course_content', 'files_visibility');
    }
    
}
