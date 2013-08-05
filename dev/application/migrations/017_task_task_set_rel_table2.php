<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_task_task_set_rel_table2 extends CI_Migration {
    
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