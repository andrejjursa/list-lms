<?php

class Migration_create_periods_courses_translations extends CI_Migration
{
    
    public function up()
    {
        $this->dbforge->add_field(
            [
                'id'      => [
                    'type'           => 'INT',
                    'constraint'     => '11',
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'updated' => [
                    'type'    => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ],
                'created' => [
                    'type'    => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ],
                'name'    => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'default'    => '',
                ],
                'sorting' => [
                    'type'       => 'INT',
                    'constraint' => '11',
                    'unsigned'   => true,
                    'default'    => 0,
                ],
            ]
        );
        
        $this->dbforge->add_key('id', true);
        
        $this->dbforge->create_table('periods');
        
        change_mysql_table_to_InnoDB('periods');
        
        $this->dbforge->add_field(
            [
                'id'                     => [
                    'type'           => 'INT',
                    'constraint'     => '11',
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'updated'                => [
                    'type'    => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ],
                'created'                => [
                    'type'    => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ],
                'name'                   => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'default'    => '',
                ],
                'period_id'              => [
                    'type'       => 'INT',
                    'constraint' => '11',
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'description'            => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'capacity'               => [
                    'type'       => 'INT',
                    'unsigned'   => true,
                    'constraint' => 4,
                    'default'    => 0,
                ],
                'groups_change_deadline' => [
                    'type' => 'timestamp',
                    'null' => true,
                ],
            ]
        );
        
        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key('period_id');
        
        $this->dbforge->create_table('courses');
        
        change_mysql_table_to_InnoDB('courses');
        
        $this->dbforge->add_field(
            [
                'idiom'    => [
                    'type'       => 'VARCHAR',
                    'constraint' => 32,
                    'default'    => '',
                ],
                'constant' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'default'    => '',
                ],
                'text'     => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
            ]
        );
        
        $this->dbforge->add_key('idiom', true);
        $this->dbforge->add_key('constant', true);
        $this->dbforge->add_key('idiom');
        $this->dbforge->add_key('constant');
        
        $this->dbforge->create_table('translations');
        
        change_mysql_table_to_InnoDB('translations');
    }
    
    public function down()
    {
        $this->dbforge->drop_table('periods');
        $this->dbforge->drop_table('courses');
        $this->dbforge->drop_table('translations');
    }
    
}
