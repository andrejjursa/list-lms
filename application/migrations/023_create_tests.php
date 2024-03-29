<?php

class Migration_create_tests extends CI_Migration
{
    
    public function up()
    {
        $this->dbforge->add_field(
            [
                'id'            => [
                    'type'           => 'INT',
                    'constraint'     => '11',
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'updated'       => [
                    'type'    => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ],
                'created'       => [
                    'type'    => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ],
                'name'          => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'default'    => '',
                ],
                'type'          => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'default'    => '',
                ],
                'subtype'       => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'default'    => '',
                ],
                'task_id'       => [
                    'type'       => 'INT',
                    'constraint' => '11',
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'configuration' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'enabled'       => [
                    'type'       => 'INT',
                    'constraint' => 1,
                    'default'    => 0,
                ],
            ]
        );
        
        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key('task_id');
        
        $this->dbforge->create_table('tests');
        
        change_mysql_table_to_InnoDB('tests');
    }
    
    public function down()
    {
        $this->dbforge->drop_table('tests');
    }
    
}
