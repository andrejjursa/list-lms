<?php

class Migration_create_task_set_permissions extends CI_Migration
{
    
    public function up()
    {
        $this->dbforge->add_field(
            [
                'id'                 => [
                    'type'           => 'INT',
                    'constraint'     => '11',
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'updated'            => [
                    'type'    => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ],
                'created'            => [
                    'type'    => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ],
                'publish_start_time' => [
                    'type' => 'timestamp',
                    'null' => true,
                ],
                'upload_end_time'    => [
                    'type' => 'timestamp',
                    'null' => true,
                ],
                'group_id'           => [
                    'type'       => 'INT',
                    'constraint' => '11',
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'room_id'            => [
                    'type'       => 'INT',
                    'constraint' => '11',
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'task_set_id'        => [
                    'type'       => 'INT',
                    'constraint' => '11',
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'enabled'            => [
                    'type'       => 'INT',
                    'constraint' => 1,
                    'default'    => 0,
                ],
            ]
        );
        
        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key('task_set_id');
        $this->dbforge->add_key('group_id');
        $this->dbforge->add_key('room_id');
        
        $this->dbforge->create_table('task_set_permissions');
        
        change_mysql_table_to_InnoDB('task_set_permissions');
    }
    
    public function down()
    {
        $this->dbforge->drop_table('task_set_permissions');
    }
    
}
