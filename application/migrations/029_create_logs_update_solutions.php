<?php

class Migration_create_logs_update_solutions extends CI_Migration
{
    
    public function up()
    {
        $this->dbforge->add_field(
            [
                'id'                      => [
                    'type'           => 'INT',
                    'constraint'     => '11',
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'updated'                 => [
                    'type'    => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ],
                'created'                 => [
                    'type'    => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ],
                'message'                 => [
                    'type' => 'text',
                    'null' => true,
                ],
                'ip_address'              => [
                    'type'       => 'varchar',
                    'constraint' => '32',
                    'default'    => '',
                ],
                'language'                => [
                    'type'       => 'varchar',
                    'constraint' => 64,
                    'default'    => '',
                ],
                'log_type'                => [
                    'type'       => 'int',
                    'constraint' => 5,
                    'unsigned'   => true,
                    'default'    => 0,
                ],
                'student_id'              => [
                    'type'       => 'int',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'teacher_id'              => [
                    'type'       => 'int',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'affected_table'          => [
                    'type'       => 'varchar',
                    'constraint' => '255',
                    'default'    => '',
                ],
                'affected_row_primary_id' => [
                    'type'       => 'varchar',
                    'constraint' => '255',
                    'default'    => '',
                ],
                'additional_data'         => [
                    'type' => 'text',
                    'null' => true,
                ],
            ]
        );
        
        $this->dbforge->add_key('id', true);
        
        $this->dbforge->create_table('logs');
        
        change_mysql_table_to_InnoDB('logs');
        
        $this->dbforge->add_column('solutions', [
            'ip_address' => [
                'type'       => 'varchar',
                'constraint' => '32',
                'default'    => '',
            ],
        ]);
    }
    
    public function down()
    {
        $this->dbforge->drop_table('logs');
        $this->dbforge->drop_column('solutions', 'ip_address');
    }
    
}
