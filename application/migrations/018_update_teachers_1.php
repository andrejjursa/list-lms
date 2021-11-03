<?php

class Migration_update_teachers_1 extends CI_Migration
{
    
    public function up()
    {
        $this->dbforge->add_column('teachers', [
            'prefered_course_id' => [
                'type'       => 'int',
                'constraint' => '11',
                'null'       => true,
                'unsigned'   => true,
            ],
        ]);
    }
    
    public function down()
    {
        $this->dbforge->drop_column('teachers', 'prefered_course_id');
    }
    
}
