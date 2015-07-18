<?php

class Migration_create_task_set_permissions extends CI_Migration {
    
    public function up() {
        $this->dbforge->add_field(
            array(
                'id' => array(
                    'type' => 'INT',
                    'constraint' => '11',
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE,
                ), 
                'updated' => array(
                    'type' => 'timestamp',
                ),  
                'created' => array(
                    'type' => 'timestamp',
                ),
                'publish_start_time' => array(
                    'type' => 'timestamp',
                    'null' => TRUE,
                ),
                'upload_end_time' => array(
                    'type' => 'timestamp',
                    'null' => TRUE,
                ),
                'group_id' => array(
                    'type' => 'INT',
                    'constraint' => '11',
                    'unsigned' => TRUE,
                    'null' => TRUE,
                ),
                'room_id' => array(
                    'type' => 'INT',
                    'constraint' => '11',
                    'unsigned' => TRUE,
                    'null' => TRUE,
                ),
                'task_set_id' => array(
                    'type' => 'INT',
                    'constraint' => '11',
                    'unsigned' => TRUE,
                    'null' => TRUE,
                ),
                'enabled' => array(
                    'type' => 'INT',
                    'constraint' => 1,
                    'defalt' => 0,
                ),
            )
        );
        
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('task_set_id');
        $this->dbforge->add_key('group_id');
        $this->dbforge->add_key('room_id');
        
        $this->dbforge->create_table('task_set_permissions');
        
        change_mysql_table_to_InnoDB('task_set_permissions');
    }
    
    public function down() {
        $this->dbforge->drop_table('task_set_permissions');
    }
    
}