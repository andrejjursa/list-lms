<?php

class Migration_update_students_1 extends CI_Migration
{
    
    public function up()
    {
        $this->dbforge->add_column('students', [
            'password_token' => [
                'type'       => 'VARCHAR',
                'constraint' => 40,
                'null'       => true,
            ],
        ]);
    }
    
    public function down()
    {
        $this->dbforge->drop_column('students', 'password_token');
    }
    
}