<?php

class Migration_create_groups_rooms extends CI_Migration
{
    
    public function up()
    {
        change_mysql_table_to_InnoDB('lang_overlays');
        
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
                'course_id' => [
                    'type'       => 'INT',
                    'constraint' => '11',
                    'null'       => true,
                    'unsigned'   => true,
                ],
            ]
        );
        
        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key('course_id');
        
        $this->dbforge->create_table('groups');
        
        change_mysql_table_to_InnoDB('groups');
        
        $this->dbforge->add_field(
            [
                'id'         => [
                    'type'           => 'INT',
                    'constraint'     => '11',
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'updated'    => [
                    'type'    => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ],
                'created'    => [
                    'type'    => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ],
                'name'       => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'default'    => '',
                ],
                'group_id'   => [
                    'type'       => 'INT',
                    'constraint' => '11',
                    'null'       => true,
                    'unsigned'   => true,
                ],
                'time_begin' => [
                    'type'       => 'INT',
                    'constraint' => 6,
                    'unsigned'   => true,
                    'default'    => 0,
                ],
                'time_end'   => [
                    'type'       => 'INT',
                    'constraint' => 6,
                    'unsigned'   => true,
                    'default'    => 0,
                ],
                'time_day'   => [
                    'type'       => 'INT',
                    'constraint' => 2,
                    'unsigned'   => true,
                    'default'    => 0,
                ],
                'capacity'   => [
                    'type'       => 'INT',
                    'constraint' => 4,
                    'unsigned'   => true,
                    'default'    => 0,
                ],
            ]
        );
        
        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key('group_id');
        
        $this->dbforge->create_table('rooms');
        
        change_mysql_table_to_InnoDB('rooms');
    }
    
    public function down()
    {
        $this->dbforge->drop_table('groups');
        $this->dbforge->drop_table('rooms');
    }
    
}
