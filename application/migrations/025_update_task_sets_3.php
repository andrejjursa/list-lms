<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_update_task_sets_3 extends CI_Migration {
    
    public function up() {
        $this->dbforge->add_column('task_sets', array(
            'allowed_test_types' => array(
                'type' => 'text',
            ),
        ));
    }
    
    public function down() {
        $this->dbforge->drop_column('task_sets', 'allowed_test_types');
    }
    
}