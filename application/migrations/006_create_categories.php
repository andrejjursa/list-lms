<?php

class Migration_create_categories extends CI_Migration
{
    
    public function up()
    {
        $this->dbforge->add_field(
            [
                'id'        => [
                    'type'           => 'INT',
                    'constraint'     => '11',
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'updated'   => [
                    'type'    => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ],
                'created'   => [
                    'type'    => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ],
                'name'      => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'default'    => '',
                ],
                'parent_id' => [
                    'type'       => 'INT',
                    'constraint' => '11',
                    'null'       => true,
                    'unsigned'   => true,
                ],
            ]
        );
        
        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key('parent_id');
        
        $this->dbforge->create_table('categories');
        
        change_mysql_table_to_InnoDB('categories');
    }
    
    public function down()
    {
        $this->dbforge->drop_table('categories');
    }
    
}
