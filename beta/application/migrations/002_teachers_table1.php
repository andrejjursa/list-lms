<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_teachers_table1 extends CI_Migration {
    
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
            )
        );
        
        $this->dbforge->add_key('id', TRUE);
        
        $this->dbforge->create_table('teachers');
        
        change_mysql_table_to_InnoDB('teachers');
    }
    
    public function down() {
        $this->dbforge->drop_table('teachers');
    }
    
}