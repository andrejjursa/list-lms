<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_create_groups_rooms extends CI_Migration {
    
    public function up() {
        change_mysql_table_to_InnoDB('lang_overlays');
        
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
                'course_id' => array(
                    'type' => 'INT',
                    'constraint' => '11',
                    'null' => TRUE,
                    'unsigned' => TRUE,
                ),
            )
        );
        
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('course_id');
        
        $this->dbforge->create_table('groups');
        
        change_mysql_table_to_InnoDB('groups');
        
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
                'group_id' => array(
                    'type' => 'INT',
                    'constraint' => '11',
                    'null' => TRUE,
                    'unsigned' => TRUE,
                ),
                'time_begin' => array(
                    'type' => 'INT',
                    'constraint' => 6,
                    'unsigned' => TRUE,
                ),
                'time_end' => array(
                    'type' => 'INT',
                    'constraint' => 6,
                    'unsigned' => TRUE,
                ),
                'time_day' => array(
                    'type' => 'INT',
                    'constraint' => 2,
                    'unsigned' => TRUE,
                ),
                'capacity' => array(
                    'type' => 'INT',
                    'constraint' => 4,
                    'unsigned' => TRUE,
                ),
            )
        );
        
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('group_id');
        
        $this->dbforge->create_table('rooms');
        
        change_mysql_table_to_InnoDB('rooms');
    }
    
    public function down() {
        $this->dbforge->drop_table('groups');
        $this->dbforge->drop_table('rooms');
    }
    
}
