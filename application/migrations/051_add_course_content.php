<?php

class Migration_add_course_content extends CI_Migration {

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
                    'type' => 'TIMESTAMP',
                    'default' => '1970-01-01 01:00:01',
                ),
                'created' => array(
                    'type' => 'TIMESTAMP',
                    'default' => '1970-01-01 01:00:01',
                ),
                'title' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'default' => '',
                ),
                'content' => array(
                    'type' => 'TEXT',
                    'null' => FALSE,
                ),
                'course_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'null' => TRUE,
                ),
                'published' => array(
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'null' => FALSE,
                    'default' => 0
                ),
                'permissions' => array(
                    'type' => 'TEXT',
                    'null' => TRUE,
                ),
            )
        );

        $this->dbforge->add_key('id', TRUE);

        $this->dbforge->add_key('course_id');

        $this->dbforge->create_table('course_content');

        change_mysql_table_to_InnoDB('course_content');
    }

    public function down() {
        $this->dbforge->drop_table('course_content');
    }

}
