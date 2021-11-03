<?php

class Migration_create_tasks_task_sets extends CI_Migration
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
                'text'    => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
            ]
        );
        
        $this->dbforge->add_key('id', true);
        
        $this->dbforge->create_table('tasks');
        
        change_mysql_table_to_InnoDB('tasks');
        
        $this->dbforge->add_field(
            [
                'id'                 => [
                    'type'           => 'INT',
                    'constraint'     => '11',
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'updated'            => [
                    'type'    => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ],
                'created'            => [
                    'type'    => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ],
                'name'               => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'default'    => '',
                ],
                'course_id'          => [
                    'type'       => 'INT',
                    'constraint' => '11',
                    'null'       => true,
                    'unsigned'   => true,
                ],
                'task_set_type_id'   => [
                    'type'       => 'INT',
                    'constraint' => '11',
                    'null'       => true,
                    'unsigned'   => true,
                ],
                'published'          => [
                    'type'       => 'INT',
                    'constraint' => 1,
                    'unsigned'   => true,
                    'default'    => 0,
                ],
                'publish_start_time' => [
                    'type' => 'timestamp',
                    'null' => true,
                ],
                'upload_end_time'    => [
                    'type' => 'timestamp',
                    'null' => true,
                ],
                'group_id'           => [
                    'type'       => 'INT',
                    'constraint' => '11',
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'room_id'            => [
                    'type'       => 'INT',
                    'constraint' => '11',
                    'unsigned'   => true,
                    'null'       => true,
                ],
            ]
        );
        
        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key('course_id');
        $this->dbforge->add_key('task_set_type_id');
        
        $this->dbforge->create_table('task_sets');
        
        change_mysql_table_to_InnoDB('task_sets');
        
        $this->dbforge->add_field(
            [
                'task_id'     => [
                    'type'       => 'INT',
                    'constraint' => '11',
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'category_id' => [
                    'type'       => 'INT',
                    'constraint' => '11',
                    'unsigned'   => true,
                    'null'       => true,
                ],
            ]
        );
        
        $this->dbforge->add_key('task_id');
        $this->dbforge->add_key('category_id');
        
        $this->dbforge->create_table('task_category_rel');
        
        change_mysql_table_to_InnoDB('task_category_rel');
        
        $this->dbforge->add_field(
            [
                'task_id'      => [
                    'type'       => 'INT',
                    'constraint' => '11',
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'task_set_id'  => [
                    'type'       => 'INT',
                    'constraint' => '11',
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'points_total' => [
                    'type'    => 'DOUBLE',
                    'default' => 0,
                ],
                'sorting'      => [
                    'type'       => 'INT',
                    'constraint' => '11',
                    'unsigned'   => true,
                    'default'    => 0,
                ],
            ]
        );
        
        $this->dbforge->add_key('task_id');
        $this->dbforge->add_key('task_set_id');
        
        $this->dbforge->create_table('task_task_set_rel');
        
        change_mysql_table_to_InnoDB('task_task_set_rel');
    }
    
    public function down()
    {
        $this->dbforge->drop_table('tasks');
        $this->dbforge->drop_table('task_sets');
        $this->dbforge->drop_table('task_category_rel');
        $this->dbforge->drop_table('task_task_set_rel');
    }
    
}
