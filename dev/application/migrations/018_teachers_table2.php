<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_teachers_table2 extends CI_Migration {
    
    public function up() {
        $this->dbforge->add_column('teachers', array(
            'prefered_course_id' => array(
                'type' => 'int',
                'constraint' => '11',
                'null' => TRUE,
                'unsigned' => TRUE,
            ),
        ));
    }
    
    public function down() {
        $this->dbforge->drop_column('teachers', 'prefered_course_id');
    }
    
}