<?php

class Migration_create_solutions extends CI_Migration
{
    
    public function up()
    {
        
        $this->dbforge->add_field([
            'id'          => [
                'type'           => 'INT',
                'constraint'     => '11',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'updated'     => [
                'type'    => 'timestamp',
                'default' => '1970-01-01 01:00:01',
            ],
            'created'     => [
                'type'    => 'timestamp',
                'default' => '1970-01-01 01:00:01',
            ],
            'task_set_id' => [
                'type'       => 'INT',
                'constraint' => '11',
                'unsigned'   => true,
                'null'       => true,
            ],
            'student_id'  => [
                'type'       => 'INT',
                'constraint' => '11',
                'unsigned'   => true,
                'null'       => true,
            ],
            'teacher_id'  => [
                'type'       => 'INT',
                'constraint' => '11',
                'unsigned'   => true,
                'null'       => true,
            ],
            'comment'     => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'points'      => [
                'type' => 'double',
                'null' => true,
            ],
            'revalidate'  => [
                'type'       => 'int',
                'constraint' => '1',
                'default'    => 0,
            ],
        ]);
        
        $this->dbforge->add_key('id', true);
        
        $this->dbforge->add_key('task_set_id');
        $this->dbforge->add_key('student_id');
        $this->dbforge->add_key('teacher_id');
        
        $this->dbforge->create_table('solutions');
        
        change_mysql_table_to_InnoDB('solutions');
        
    }
    
    public function down()
    {
        $this->dbforge->drop_table('solutions');
    }
    
}
