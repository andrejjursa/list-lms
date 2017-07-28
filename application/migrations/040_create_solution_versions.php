<?php

class Migration_create_solution_versions extends CI_Migration {

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
                'solution_id' => array(
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
                'download_lock' => array(
                    'type' => 'int',
                    'constraint' => '1',
                    'unsigned' => TRUE,
                    'default' => 0,
                ),
                'ip_address' => array(
                    'type' => 'varchar',
                    'constraint' => '32',
                    'null' => TRUE,
                ),
            )
        );

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('solution_id');
        $this->dbforge->add_key('version');

        $this->dbforge->create_table('solution_versions');

        change_mysql_table_to_InnoDB('solution_versions');
    }

    public function down() {
        $this->dbforge->drop_table('solution_versions');
    }

}
