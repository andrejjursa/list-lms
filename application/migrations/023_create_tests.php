<?php

class Migration_create_tests extends CI_Migration {
    
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
                ),
                'type' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                ),
                'subtype' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                ),
                'task_id' => array(
                    'type' => 'INT',
                    'constraint' => '11',
                    'unsigned' => TRUE,
                    'null' => TRUE,
                ),
                'configuration' => array(
                    'type' => 'TEXT',
                    'null' => FALSE,
                ),
                'enabled' => array(
                    'type' => 'INT',
                    'constraint' => 1,
                    'defalt' => 0,
                ),
            )
        );
        
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('task_id');
        
        $this->dbforge->create_table('tests');
        
        change_mysql_table_to_InnoDB('tests');
    }
    
    public function down() {
        $this->dbforge->drop_table('tests');
    }
    
}