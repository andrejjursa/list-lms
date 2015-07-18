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
                ),  
                'created' => array(
                    'type' => 'timestamp',
                ),
                'ip_addresses' => array(
                    'type' => 'text',
                ),
                'start_time' => array(
                    'type' => 'timestamp',
                ),
                'end_time' => array(
                    'type' => 'timestamp',
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
