<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_create_solutions extends CI_Migration {

    public function up() {

        $this->dbforge->add_field(array(
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
            'task_set_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
                'null' => TRUE,
            ),
            'student_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
                'null' => TRUE,
            ),
            'teacher_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
                'null' => TRUE,
            ),
            'comment' => array(
                'type' => 'TEXT',
                'null' => TRUE,
            ),
            'points' => array(
                'type' => 'double',
                'null' => TRUE,
            ),
            'revalidate' => array(
                'type' => 'int',
                'constraint' => '1',
                'default' => 0,
            ),
        ));

        $this->dbforge->add_key('id', TRUE);

        $this->dbforge->add_key('task_set_id');
        $this->dbforge->add_key('student_id');
        $this->dbforge->add_key('teacher_id');

        $this->dbforge->create_table('solutions');

        change_mysql_table_to_InnoDB('solutions');

    }

    public function down() {
        $this->dbforge->drop_table('solutions');
    }

}
