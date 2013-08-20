<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_update_students_1 extends CI_Migration {
    
    public function up() {
        $this->dbforge->add_column('students', array(
            'password_token' => array(
                'type' => 'VARCHAR',
                'constraint' => 40,
                'null' => TRUE,
            ),
        ));
    }
    
    public function down() {
        $this->dbforge->drop_column('students', 'password_token');
    }
    
}