<?php

class Migration_create_students extends CI_Migration
{
    
    public function up()
    {
        $this->dbforge->add_field(
            [
                'id'               => [
                    'type'           => 'INT',
                    'constraint'     => '11',
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'updated'          => [
                    'type'    => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ],
                'created'          => [
                    'type'    => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ],
                'fullname'         => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'default'    => '',
                ],
                'email'            => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'default'    => '',
                ],
                'password'         => [
                    'type'       => 'VARCHAR',
                    'constraint' => 40,
                    'default'    => '',
                ],
                'language'         => [
                    'type'       => 'VARCHAR',
                    'constraint' => 32,
                    'default'    => '',
                ],
                'active_course_id' => [
                    'type'       => 'INT',
                    'constraint' => '11',
                    'unsigned'   => true,
                    'null'       => true,
                ],
            ]
        );
        
        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key('active_course_id');
        
        $this->dbforge->create_table('students');
        
        change_mysql_table_to_InnoDB('students');
    }
    
    public function down()
    {
        $this->dbforge->drop_table('students');
    }
    
}
