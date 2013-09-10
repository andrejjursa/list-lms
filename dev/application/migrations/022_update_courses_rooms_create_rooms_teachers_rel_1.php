<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_update_courses_rooms_create_rooms_teachers_rel_1 extends CI_Migration {
    
    public function up() {
        $this->dbforge->add_column('courses', array(
            'allow_subscription_to' => array(
                'type' => 'timestamp',
                'null' => TRUE,
            ),
        ));
        
        $this->dbforge->add_column('rooms', array(
            'teachers_plain' => array(
                'type' => 'text',
                'null' => TRUE,
            ),
        ));
        
        $this->dbforge->add_field(
            array(
                'room_id' => array(
                    'type' => 'INT',
                    'constraint' => '11',
                    'unsigned' => TRUE,
                    'null' => TRUE,
                ),
                'teacher_id' => array(
                    'type' => 'INT',
                    'constraint' => '11',
                    'unsigned' => TRUE,
                    'null' => TRUE,
                ),
            )
        );
        
        $this->dbforge->add_key('room_id');
        $this->dbforge->add_key('teacher_id');
        
        $this->dbforge->create_table('rooms_teachers_rel');
        
        change_mysql_table_to_InnoDB('rooms_teachers_rel');
    }
    
    public function down() {
        $this->dbforge->drop_column('courses', 'allow_subscription_to');
        $this->dbforge->drop_column('rooms', 'teachers_plain');
        $this->dbforge->drop_table('rooms_teachers_rel');
    }
    
}