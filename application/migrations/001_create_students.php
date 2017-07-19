<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_create_students extends CI_Migration {

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
                'fullname' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'default' => '',
                ),
                'email' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'default' => '',
                ),
                'password' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 40,
                    'default' => '',
                ),
                'language' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 32,
                    'default' => '',
                ),
                'active_course_id' => array(
                    'type' => 'INT',
                    'constraint' => '11',
                    'unsigned' => TRUE,
                    'null' => TRUE,
                ),
            )
        );

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('active_course_id');

        $this->dbforge->create_table('students');

        change_mysql_table_to_InnoDB('students');
    }

    public function down() {
        $this->dbforge->drop_table('students');
    }

}
