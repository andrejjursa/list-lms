<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_students_table1 extends CI_Migration {
    
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
                    'type' => 'varchar',
                    'constraint' => 255,
                ),
                'password' => array(
                    'type' => 'varchar',
                    'constraint' => 40,
                ),
            )
        );
        
        $this->dbforge->add_key('id', TRUE);
        
        $this->dbforge->create_table('students');
    }
    
    public function down() {
        $this->dbforge->drop_table('students');
    }
    
}