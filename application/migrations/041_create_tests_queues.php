<?php

class Migration_create_tests_queues extends CI_Migration
{
    
    public function up()
    {
        $this->dbforge->add_field(
            [
                'id'                => [
                    'type'           => 'INT',
                    'constraint'     => '11',
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'updated'           => [
                    'type'    => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ],
                'created'           => [
                    'type'    => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ],
                'start'             => [
                    'type'    => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ],
                'exec_start'        => [
                    'type'    => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ],
                'finish'            => [
                    'type'    => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ],
                'test_type'         => [
                    'type'       => 'varchar',
                    'constraint' => '64',
                    'default'    => '',
                ],
                'task_set_id'       => [
                    'type'       => 'int',
                    'constraint' => '11',
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'student_id'        => [
                    'type'       => 'int',
                    'constraint' => '11',
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'version'           => [
                    'type'       => 'int',
                    'constraint' => '11',
                    'unsigned'   => true,
                    'default'    => 0,
                ],
                'task_id'           => [
                    'type'       => 'int',
                    'constraint' => '11',
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'teacher_id'        => [
                    'type'       => 'int',
                    'constraint' => '11',
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'priority'          => [
                    'type'       => 'int',
                    'constraint' => '4',
                    'unsigned'   => true,
                    'default'    => 2,
                ],
                'original_priority' => [
                    'type'       => 'int',
                    'constraint' => '4',
                    'unsigned'   => true,
                    'default'    => 2,
                ],
                'worker'            => [
                    'type'       => 'int',
                    'constraint' => '11',
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'points'            => [
                    'type'    => 'double',
                    'default' => 0,
                ],
                'bonus'             => [
                    'type'    => 'double',
                    'default' => 0,
                ],
                'status'            => [
                    'type'       => 'int',
                    'unsigned'   => true,
                    'constraint' => '4',
                    'default'    => 0,
                ],
                'system_language'   => [
                    'type'       => 'varchar',
                    'constraint' => '32',
                    'default'    => '',
                ],
            ]
        );
        
        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key('task_set_id');
        $this->dbforge->add_key('student_id');
        $this->dbforge->add_key('task_id');
        $this->dbforge->add_key('teacher_id');
        $this->dbforge->add_key('priority');
        $this->dbforge->add_key('worker');
        
        $this->dbforge->create_table('tests_queue');
        
        change_mysql_table_to_InnoDB('tests_queue');
        
        $this->dbforge->add_column('task_sets', [
            'test_priority' => [
                'type'       => 'int',
                'constraint' => '4',
                'unsigned'   => true,
                'default'    => 2,
            ],
        ]);
        
        $this->dbforge->add_field(
            [
                'test_id'        => [
                    'type'       => 'INT',
                    'constraint' => '11',
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'test_queue_id'  => [
                    'type'       => 'INT',
                    'constraint' => '11',
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'result'         => [
                    'type'       => 'int',
                    'constraint' => '8',
                    'unsigned'   => true,
                    'default'    => 0,
                ],
                'result_text'    => [
                    'type' => 'text',
                    'null' => true,
                ],
                'percent_points' => [
                    'type'    => 'double',
                    'default' => 0,
                ],
                'percent_bonus'  => [
                    'type'    => 'double',
                    'default' => 0,
                ],
                'points'         => [
                    'type'    => 'double',
                    'default' => 0,
                ],
                'bonus'          => [
                    'type'    => 'double',
                    'default' => 0,
                ],
            ]
        );
        
        $this->dbforge->add_key('test_id');
        $this->dbforge->add_key('test_queue_id');
        
        $this->dbforge->create_table('test_test_queue_rel');
        
        change_mysql_table_to_InnoDB('test_test_queue_rel');
    }
    
    public function down()
    {
        $this->dbforge->drop_table('tests_queue');
        $this->dbforge->drop_column('task_sets', 'test_priority');
        $this->dbforge->drop_table('test_test_queue_rel');
    }
    
}
