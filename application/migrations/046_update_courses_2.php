<?php

class Migration_update_courses_2 extends CI_Migration
{
    
    public function up()
    {
        $this->dbforge->add_column('courses', [
            'auto_accept_students' => [
                'type'       => 'int',
                'default'    => '0',
                'constraint' => true,
            ],
        ]);
    }
    
    public function down()
    {
        $this->dbforge->drop_column('courses', 'auto_accept_students');
    }
    
}
