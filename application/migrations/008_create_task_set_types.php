<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_create_task_set_types extends CI_Migration {
    
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
                    'default' => '1970-01-01 01:00:01',
                ),  
                'created' => array(
                    'type' => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ),
                'name' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                ),
            )
        );
        
        $this->dbforge->add_key('id', TRUE);
        
        $this->dbforge->create_table('task_set_types');
        
        change_mysql_table_to_InnoDB('task_set_types');
        
        $this->dbforge->add_field(
            array(
                'course_id' => array(
                    'type' => 'INT',
                    'constraint' => '11',
                    'unsigned' => TRUE,
                    'null' => TRUE,
                ),
                'task_set_type_id' => array(
                    'type' => 'INT',
                    'constraint' => '11',
                    'unsigned' => TRUE,
                    'null' => TRUE,
                ),
                'upload_solution' => array(
                    'type' => 'INT',
                    'constraing' => '1',
                    'unsigned' => TRUE,
                    'default' => 1,
                ),
            )
        );
        
        $this->dbforge->add_key('course_id');
        $this->dbforge->add_key('task_set_type_id');
        
        $this->dbforge->create_table('course_task_set_type_rel');
        
        change_mysql_table_to_InnoDB('course_task_set_type_rel');
    }
    
    public function down() {
        $this->dbforge->drop_table('task_set_types');
        $this->dbforge->drop_table('course_task_set_type_rel');
    }
    
}