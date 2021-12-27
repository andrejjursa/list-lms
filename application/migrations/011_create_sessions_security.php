<?php

class Migration_create_sessions_security extends CI_Migration
{
    
    public function up()
    {
        
        $this->dbforge->add_field([
            'session_id'    => [
                'type'       => 'varchar',
                'constraint' => '40',
                'default'    => '0',
                'null'       => false,
            ],
            'ip_address'    => [
                'type'       => 'varchar',
                'constraint' => '45',
                'default'    => '0',
                'null'       => false,
            ],
            'user_agent'    => [
                'type'       => 'varchar',
                'constraint' => '120',
                'null'       => true,
            ],
            'last_activity' => [
                'type'       => 'int',
                'constraint' => '10',
                'unsigned'   => true,
                'default'    => 0,
                'null'       => false,
            ],
            'user_data'     => [
                'type' => 'text',
                'null' => true,
            ],
        ]);
        
        $this->dbforge->add_key('session_id', true);
        $this->dbforge->add_key('last_activity');
        
        $this->dbforge->create_table('sessions');
        
        change_mysql_table_to_InnoDB('sessions');
        
        $this->dbforge->add_field([
            'id'                => [
                'type'           => 'INT',
                'constraint'     => '11',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'updated'           => [
                'type'    => 'timestamp',
                'default' => '1970-01-01 01:00:01',
            ],
            'created'           => [
                'type'    => 'timestamp',
                'default' => '1970-01-01 01:00:01',
            ],
            'account_type'      => [
                'type'       => 'varchar',
                'constraint' => '32',
                'default'    => '',
            ],
            'account_email'     => [
                'type'       => 'varchar',
                'constraint' => '255',
                'default'    => '',
            ],
            'login_ip_address'  => [
                'type'       => 'varchar',
                'constraint' => '32',
                'default'    => '',
            ],
            'login_browser'     => [
                'type'       => 'varchar',
                'constraint' => '255',
                'default'    => '',
            ],
            'login_failed_time' => [
                'type'    => 'timestamp',
                'default' => '1970-01-01 01:00:01',
            ],
        ]);
        
        $this->dbforge->add_key('id', true);
        
        $this->dbforge->create_table('security');
        
        change_mysql_table_to_InnoDB('security');
    }
    
    public function down()
    {
        
        $this->dbforge->drop_table('sessions');
        $this->dbforge->drop_table('security');
        
    }
    
}
