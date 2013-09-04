<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_update_courses_1 extends CI_Migration {
    
    public function up() {
        $this->dbforge->add_column('courses', array(
            'default_points_to_remove' => array(
                'type' => 'double',
                'default' => '3',
            ),
        ));
    }
    
    public function down() {
        $this->dbforge->drop_column('courses', 'default_points_to_remove');
    }
    
}