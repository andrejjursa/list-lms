<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_periods_courses_translations_table1 extends CI_Migration {
    
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
            )
        );
        
        $this->dbforge->add_key('id', TRUE);
        
        $this->dbforge->create_table('periods');
        
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
            )
        );
        
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('period_id');
        
        $this->dbforge->create_table('courses');
        
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
    }
    
    public function down() {
        $this->dbforge->drop_table('periods');
        $this->dbforge->drop_table('courses');
        $this->dbforge->drop_table('translations');
    }
    
}