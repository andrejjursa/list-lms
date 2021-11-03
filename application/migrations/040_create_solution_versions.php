<?php

class Migration_create_solution_versions extends CI_Migration
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
                'solution_id'   => [
                    'type'       => 'int',
                    'constraint' => '11',
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'version'       => [
                    'type'       => 'int',
                    'constraint' => '11',
                    'unsigned'   => true,
                    'default'    => 0,
                ],
                'download_lock' => [
                    'type'       => 'int',
                    'constraint' => '1',
                    'unsigned'   => true,
                    'default'    => 0,
                ],
                'ip_address'    => [
                    'type'       => 'varchar',
                    'constraint' => '32',
                    'null'       => true,
                ],
            ]
        );
        
        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key('solution_id');
        $this->dbforge->add_key('version');
        
        $this->dbforge->create_table('solution_versions');
        
        change_mysql_table_to_InnoDB('solution_versions');
    }
    
    public function down()
    {
        $this->dbforge->drop_table('solution_versions');
    }
    
}
