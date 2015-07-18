<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_update_task_task_set_rel_1 extends CI_Migration {
    
    public function up() {
        $this->dbforge->add_column('task_task_set_rel', array(
            'bonus_task' => array(
                'type' => 'int',
                'constraint' => '1',
            ),
        ));
    }
    
    public function down() {
        $this->dbforge->drop_column('task_task_set_rel', 'bonus_task');
    }
    
}