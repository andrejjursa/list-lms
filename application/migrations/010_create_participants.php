<?php

class Migration_create_participants extends CI_Migration
{
    
    public function up()
    {
        $this->dbforge->add_field(
            [
                'id'         => [
                    'type'           => 'INT',
                    'constraint'     => '11',
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'updated'    => [
                    'type'    => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ],
                'created'    => [
                    'type'    => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ],
                'student_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'course_id'  => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'group_id'   => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'allowed'    => [
                    'type'       => 'INT',
                    'constraint' => 1,
                    'unsigned'   => true,
                    'default'    => 0,
                ],
            ]
        );
        
        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key('student_id');
        $this->dbforge->add_key('course_id');
        $this->dbforge->add_key('group_id');
        
        $this->dbforge->create_table('participants');
        
        change_mysql_table_to_InnoDB('participants');
    }
    
    public function down()
    {
        $this->dbforge->drop_table('participants');
    }
    
}
