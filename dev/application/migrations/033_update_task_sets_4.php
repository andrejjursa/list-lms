<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_update_task_sets_4 extends CI_Migration {
    
    public function up() {
        $this->dbforge->add_column('task_sets', array(
            'test_min_needed' => array(
                'type' => 'int',
                'unsigned' => TRUE,
                'constraint' => 6,
            ),
            'test_max_allowed' => array(
                'type' => 'int',
                'unsigned' => TRUE,
                'constraint' => 6,
            ),
        ));
    }
    
    public function down() {
        $this->dbforge->drop_column('task_sets', 'test_min_needed');
        $this->dbforge->drop_column('task_sets', 'test_max_allowed');
    }
    
}