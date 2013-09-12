<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_create_periods_courses_translations extends CI_Migration {
    
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
                'name' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                ),
                'sorting' => array(
                    'type' => 'INT',
                    'constraint' => '11',
                    'unsigned' => TRUE,
                ),
            )
        );
        
        $this->dbforge->add_key('id', TRUE);
        
        $this->dbforge->create_table('periods');
        
        change_mysql_table_to_InnoDB('periods');
        
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
                'name' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                ),
                'period_id' => array(
                    'type' => 'INT',
                    'constraint' => '11',
                    'unsigned' => TRUE,
                    'null' => TRUE
                ),
                'description' => array(
                    'type' => 'TEXT'
                ),
                'capacity' => array(
                    'type' => 'INT',
                    'unsigned' => TRUE,
                    'constraint' => 4,
                ),
                'groups_change_deadline' => array(
                    'type' => 'timestamp',
                    'null' => TRUE,
                ),
            )
        );
        
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('period_id');
        
        $this->dbforge->create_table('courses');
        
        change_mysql_table_to_InnoDB('courses');
        
        $this->dbforge->add_field(
            array(
                'idiom' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 32,
                ),
                'constant' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                ),
                'text' => array(
                    'type' => 'TEXT',
                ),
            )
        );
        
        $this->dbforge->add_key('idiom', TRUE);
        $this->dbforge->add_key('constant', TRUE);
        $this->dbforge->add_key('idiom');
        $this->dbforge->add_key('constant');
        
        $this->dbforge->create_table('translations');
        
        change_mysql_table_to_InnoDB('translations');
    }
    
    public function down() {
        $this->dbforge->drop_table('periods');
        $this->dbforge->drop_table('courses');
        $this->dbforge->drop_table('translations');
    }
    
}