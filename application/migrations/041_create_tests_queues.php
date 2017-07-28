<?php

class Migration_create_tests_queues extends CI_Migration {

    public function up() {
        $this->dbforge->add_field(
            array(
                'id' => array(
                    'type' => 'INT',
                    'constraint' => '11',
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE,
                ),
                'updated' => array(
                    'type' => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ),
                'created' => array(
                    'type' => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ),
                'start' => array(
                    'type' => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ),
                'exec_start' => array(
                    'type' => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ),
                'finish' => array(
                    'type' => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ),
                'test_type' => array(
                    'type' => 'varchar',
                    'constraint' => '64',
                    'default' => '',
                ),
                'task_set_id' => array(
                    'type' => 'int',
                    'constraint' => '11',
                    'unsigned' => TRUE,
                    'null' => TRUE,
                ),
                'student_id' => array(
                    'type' => 'int',
                    'constraint' => '11',
                    'unsigned' => TRUE,
                    'null' => TRUE,
                ),
                'version' => array(
                    'type' => 'int',
                    'constraint' => '11',
                    'unsigned' => TRUE,
                    'default' => 0,
                ),
                'task_id' => array(
                    'type' => 'int',
                    'constraint' => '11',
                    'unsigned' => TRUE,
                    'null' => TRUE,
                ),
                'teacher_id' => array(
                    'type' => 'int',
                    'constraint' => '11',
                    'unsigned' => TRUE,
                    'null' => TRUE,
                ),
                'priority' => array(
                    'type' => 'int',
                    'constraint' => '4',
                    'unsigned' => TRUE,
                    'default' => 2,
                ),
                'original_priority' => array(
                    'type' => 'int',
                    'constraint' => '4',
                    'unsigned' => TRUE,
                    'default' => 2,
                ),
                'worker' => array(
                    'type' => 'int',
                    'constraint' => '11',
                    'unsigned' => TRUE,
                    'null' => TRUE,
                ),
                'points' => array(
                    'type' => 'double',
                    'default' => 0,
                ),
                'bonus' => array(
                    'type' => 'double',
                    'default' => 0,
                ),
                'status' => array(
                    'type' => 'int',
                    'unsigned' => TRUE,
                    'constraint' => '4',
                    'default' => 0,
                ),
                'system_language' => array(
                    'type' => 'varchar',
                    'constraint' => '32',
                    'default' => '',
                )
            )
        );

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('task_set_id');
        $this->dbforge->add_key('student_id');
        $this->dbforge->add_key('task_id');
        $this->dbforge->add_key('teacher_id');
        $this->dbforge->add_key('priority');
        $this->dbforge->add_key('worker');

        $this->dbforge->create_table('tests_queue');

        change_mysql_table_to_InnoDB('tests_queue');

        $this->dbforge->add_column('task_sets', array(
            'test_priority' => array(
                'type' => 'int',
                'constraint' => '4',
                'unsigned' => TRUE,
                'default' => 2,
            ),
        ));

        $this->dbforge->add_field(
            array(
                'test_id' => array(
                    'type' => 'INT',
                    'constraint' => '11',
                    'unsigned' => TRUE,
                    'null' => TRUE,
                ),
                'test_queue_id' => array(
                    'type' => 'INT',
                    'constraint' => '11',
                    'unsigned' => TRUE,
                    'null' => TRUE,
                ),
                'result' => array(
                    'type' => 'int',
                    'constraint' => '8',
                    'unsigned' => TRUE,
                    'default' => 0,
                ),
                'result_text' => array(
                    'type' => 'text',
                    'null' => TRUE,
                ),
                'percent_points' => array(
                    'type' => 'double',
                    'default' => 0,
                ),
                'percent_bonus' => array(
                    'type' => 'double',
                    'default' => 0,
                ),
                'points' => array(
                    'type' => 'double',
                    'default' => 0,
                ),
                'bonus' => array(
                    'type' => 'double',
                    'default' => 0,
                ),
            )
        );

        $this->dbforge->add_key('test_id');
        $this->dbforge->add_key('test_queue_id');

        $this->dbforge->create_table('test_test_queue_rel');

        change_mysql_table_to_InnoDB('test_test_queue_rel');
    }

    public function down() {
        $this->dbforge->drop_table('tests_queue');
        $this->dbforge->drop_column('task_sets', 'test_priority');
        $this->dbforge->drop_table('test_test_queue_rel');
    }

}
