<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_task_sets_table2 extends CI_Migration {
    
    public function up() {
        $this->dbforge->add_column('task_sets', array(
            'instructions' => array(
                'type' => 'text',
            ),
        ));
    }
    
    public function down() {
        $this->dbforge->drop_column('task_sets', 'instructions');
    }
    
}