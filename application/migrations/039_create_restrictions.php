<?php

class Migration_create_restrictions extends CI_Migration {

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
                'ip_addresses' => array(
                    'type' => 'text',
                ),
                'start_time' => array(
                    'type' => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ),
                'end_time' => array(
                    'type' => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ),
            )
        );
        
        $this->dbforge->add_key('id', TRUE);
        
        $this->dbforge->create_table('restrictions');
        
        change_mysql_table_to_InnoDB('restrictions');
    }
    
    public function down() {
        $this->dbforge->drop_table('restrictions');
    }
    
}
