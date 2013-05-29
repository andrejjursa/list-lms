<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_participants_table1 extends CI_Migration {

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
                'student_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'null' => TRUE,
                ),
                'course_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'null' => TRUE,
                ),
                'group_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'null' => TRUE,
                ),
                'allowed' => array(
                    'type' => 'INT',
                    'constraint' => 1,
                    'unsigned' => TRUE,
                ),
            )
        );
        
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('student_id');
        $this->dbforge->add_key('course_id');
        $this->dbforge->add_key('group_id');
        
        $this->dbforge->create_table('participants');
        
        change_mysql_table_to_InnoDB('participants');
    }
    
    public function down() {
        $this->dbforge->drop_table('participants');
    }
    
}