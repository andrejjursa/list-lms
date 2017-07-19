<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_create_tasks_task_sets extends CI_Migration {

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
                'name' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'default' => '',
                ),
                'text' => array(
                    'type' => 'TEXT',
                    'null' => true,
                ),
            )
        );

        $this->dbforge->add_key('id', TRUE);

        $this->dbforge->create_table('tasks');

        change_mysql_table_to_InnoDB('tasks');

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
                'name' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'default' => '',
                ),
            	'course_id' => array(
                    'type' => 'INT',
                    'constraint' => '11',
                    'null' => TRUE,
                    'unsigned' => TRUE,
            	),
            	'task_set_type_id' => array(
                    'type' => 'INT',
                    'constraint' => '11',
                    'null' => TRUE,
                    'unsigned' => TRUE,
            	),
                'published' => array(
                    'type' => 'INT',
                    'constraint' => 1,
                    'unsigned' => TRUE,
                ),
                'publish_start_time' => array(
                    'type' => 'timestamp',
                    'null' => TRUE,
                ),
                'upload_end_time' => array(
                    'type' => 'timestamp',
                    'null' => TRUE,
                ),
                'group_id' => array(
                    'type' => 'INT',
                    'constraint' => '11',
                    'unsigned' => TRUE,
                    'null' => TRUE,
                ),
                'room_id' => array(
                    'type' => 'INT',
                    'constraint' => '11',
                    'unsigned' => TRUE,
                    'null' => TRUE,
                ),
            )
        );

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('course_id');
        $this->dbforge->add_key('task_set_type_id');

        $this->dbforge->create_table('task_sets');

        change_mysql_table_to_InnoDB('task_sets');

        $this->dbforge->add_field(
            array(
                'task_id' => array(
                    'type' => 'INT',
                    'constraint' => '11',
                    'unsigned' => TRUE,
                    'null' => TRUE,
                ),
                'category_id' => array(
                    'type' => 'INT',
                    'constraint' => '11',
                    'unsigned' => TRUE,
                    'null' => TRUE,
                ),
            )
        );

        $this->dbforge->add_key('task_id');
        $this->dbforge->add_key('category_id');

        $this->dbforge->create_table('task_category_rel');

        change_mysql_table_to_InnoDB('task_category_rel');

        $this->dbforge->add_field(
            array(
                'task_id' => array(
                    'type' => 'INT',
                    'constraint' => '11',
                    'unsigned' => TRUE,
                    'null' => TRUE,
                ),
                'task_set_id' => array(
                    'type' => 'INT',
                    'constraint' => '11',
                    'unsigned' => TRUE,
                    'null' => TRUE,
                ),
                'points_total' => array(
                    'type' => 'DOUBLE',
                    'default' => 0,
                ),
                'sorting' => array(
                    'type' => 'INT',
                    'constraint' => '11',
                    'unsigned' => TRUE,
                    'default' => 0,
                ),
            )
        );

        $this->dbforge->add_key('task_id');
        $this->dbforge->add_key('task_set_id');

        $this->dbforge->create_table('task_task_set_rel');

        change_mysql_table_to_InnoDB('task_task_set_rel');
    }

    public function down() {
        $this->dbforge->drop_table('tasks');
        $this->dbforge->drop_table('task_sets');
        $this->dbforge->drop_table('task_category_rel');
        $this->dbforge->drop_table('task_task_set_rel');
    }

}
