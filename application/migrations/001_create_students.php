<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_create_students extends CI_Migration {
    
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
                'fullname' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                ),
                'email' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                ),
                'password' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 40,
                ),
                'language' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 32,
                ),
                'active_course_id' => array(
                    'type' => 'INT',
                    'constraint' => '11',
                    'unsigned' => TRUE,
                    'null' => TRUE,
                ),
            )
        );
        
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('active_course_id');
        
        $this->dbforge->create_table('students');
        
        change_mysql_table_to_InnoDB('students');
    }
    
    public function down() {
        $this->dbforge->drop_table('students');
    }
    
}