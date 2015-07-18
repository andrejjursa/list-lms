<?php

class Migration_create_sessions_security extends CI_Migration {
    
    public function up() {
        
        $this->dbforge->add_field(array(
            'session_id' => array(
                'type' => 'varchar',
                'constraint' => '40',
                'default' => '0',
                'null' => FALSE,
            ),
            'ip_address' => array(
                'type' => 'varchar',
                'constraint' => '45',
                'default' => '0',
                'null' => FALSE,
            ),
            'user_agent' => array(
                'type' => 'varchar',
                'constraint' => '120',
                'null' => FALSE,
            ),
            'last_activity' => array(
                'type' => 'int',
                'constraint' => '10',
                'unsigned' => TRUE,
                'default' => 0,
                'null' => FALSE,
            ),
            'user_data' => array(
                'type' => 'text',
                'null' => FALSE,
            ),
        ));
        
        $this->dbforge->add_key('session_id', TRUE);
        $this->dbforge->add_key('last_activity');
        
        $this->dbforge->create_table('sessions');
        
        change_mysql_table_to_InnoDB('sessions');
        
        $this->dbforge->add_field(array(
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
            'account_type' => array(
                'type' => 'varchar',
                'constraint' => '32',
            ),
            'account_email' => array(
                'type' => 'varchar',
                'constraint' => '255',
            ),
            'login_ip_address' => array(
                'type' => 'varchar',
                'constraint' => '32',
            ),
            'login_browser' => array(
                'type' => 'varchar',
                'constraint' => '255',
            ),
            'login_failed_time' => array(
                'type' => 'timestamp',
            )
        ));
        
        $this->dbforge->add_key('id', TRUE);
        
        $this->dbforge->create_table('security');
        
        change_mysql_table_to_InnoDB('security');
    }
    
    public function down() {
        
        $this->dbforge->drop_table('sessions');
        $this->dbforge->drop_table('security');
        
    }
    
}
