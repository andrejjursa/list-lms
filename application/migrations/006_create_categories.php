<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_create_categories extends CI_Migration {
    
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
                'parent_id' => array(
                    'type' => 'INT',
                    'constraint' => '11',
                    'null' => TRUE,
                    'unsigned' => TRUE,
                ),
            )
        );
        
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('parent_id');
        
        $this->dbforge->create_table('categories');
        
        change_mysql_table_to_InnoDB('categories');
    }
    
    public function down() {
        $this->dbforge->drop_table('categories');
    }
    
}