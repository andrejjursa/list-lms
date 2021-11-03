<?php

class Migration_update_courses_3 extends CI_Migration
{
    
    public function up()
    {
        $this->dbforge->add_column('courses', [
            'syllabus'     => [
                'type' => 'text',
                'null' => true,
            ],
            'grading'      => [
                'type' => 'text',
                'null' => true,
            ],
            'instructions' => [
                'type' => 'text',
                'null' => true,
            ],
            'other_texts'  => [
                'type' => 'text',
                'null' => true,
            ],
        ]);
    }
    
    public function down()
    {
        $this->dbforge->drop_column('courses', 'syllabus');
        $this->dbforge->drop_column('courses', 'grading');
        $this->dbforge->drop_column('courses', 'instructions');
        $this->dbforge->drop_column('courses', 'other_texts');
    }
    
}