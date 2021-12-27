<?php

class Migration_create_task_set_types extends CI_Migration
{
    
    public function up()
    {
        $this->dbforge->add_field(
            [
                'id'      => [
                    'type'           => 'INT',
                    'constraint'     => '11',
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'updated' => [
                    'type'    => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ],
                'created' => [
                    'type'    => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ],
                'name'    => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'default'    => '',
                ],
            ]
        );
        
        $this->dbforge->add_key('id', true);
        
        $this->dbforge->create_table('task_set_types');
        
        change_mysql_table_to_InnoDB('task_set_types');
        
        $this->dbforge->add_field(
            [
                'course_id'        => [
                    'type'       => 'INT',
                    'constraint' => '11',
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'task_set_type_id' => [
                    'type'       => 'INT',
                    'constraint' => '11',
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'upload_solution'  => [
                    'type'       => 'INT',
                    'constraing' => '1',
                    'unsigned'   => true,
                    'default'    => 1,
                ],
            ]
        );
        
        $this->dbforge->add_key('course_id');
        $this->dbforge->add_key('task_set_type_id');
        
        $this->dbforge->create_table('course_task_set_type_rel');
        
        change_mysql_table_to_InnoDB('course_task_set_type_rel');
    }
    
    public function down()
    {
        $this->dbforge->drop_table('task_set_types');
        $this->dbforge->drop_table('course_task_set_type_rel');
    }
    
}
