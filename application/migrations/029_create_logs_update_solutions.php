<?php

class Migration_create_logs_update_solutions extends CI_Migration {

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
                'message' => array(
                    'type' => 'text',
                    'null' => true,
                ),
                'ip_address' => array(
                    'type' => 'varchar',
                    'constraint' => '32',
                    'default' => '',
                ),
                'language' => array(
                    'type' => 'varchar',
                    'constraint' => 64,
                    'default' => '',
                ),
                'log_type' => array(
                    'type' => 'int',
                    'constraint' => 5,
                    'unsigned' => TRUE,
                ),
                'student_id' => array(
                    'type' => 'int',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'null' => TRUE,
                ),
                'teacher_id' => array(
                    'type' => 'int',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'null' => TRUE,
                ),
                'affected_table' => array(
                    'type' => 'varchar',
                    'constraint' => '255',
                    'default' => '',
                ),
                'affected_row_primary_id' => array(
                    'type' => 'varchar',
                    'constraint' => '255',
                    'default' => '',
                ),
                'additional_data' => array(
                    'type' => 'text',
                    'null' => true,
                ),
            )
        );

        $this->dbforge->add_key('id', TRUE);

        $this->dbforge->create_table('logs');

        change_mysql_table_to_InnoDB('logs');

        $this->dbforge->add_column('solutions', array(
            'ip_address' => array(
                'type' => 'varchar',
                'constraint' => '32',
            ),
        ));
    }

    public function down() {
        $this->dbforge->drop_table('logs');
        $this->dbforge->drop_column('solutions', 'ip_address');
    }

}
