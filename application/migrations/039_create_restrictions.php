<?php

class Migration_create_restrictions extends CI_Migration
{
    
    public function up()
    {
        $this->dbforge->add_field(
            [
                'id'           => [
                    'type'           => 'INT',
                    'constraint'     => '11',
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'updated'      => [
                    'type'    => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ],
                'created'      => [
                    'type'    => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ],
                'ip_addresses' => [
                    'type' => 'text',
                    'null' => true,
                ],
                'start_time'   => [
                    'type'    => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ],
                'end_time'     => [
                    'type'    => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ],
            ]
        );
        
        $this->dbforge->add_key('id', true);
        
        $this->dbforge->create_table('restrictions');
        
        change_mysql_table_to_InnoDB('restrictions');
    }
    
    public function down()
    {
        $this->dbforge->drop_table('restrictions');
    }
    
}
