<?php

class Migration_update_tasks_task_sets_task_task_set_rel_1 extends CI_Migration
{
    
    public function up()
    {
        $this->dbforge->add_column('task_sets', [
            'internal_comment' => [
                'type' => 'text',
                'null' => true,
            ],
        ]);
        
        $this->dbforge->add_column('tasks', [
            'internal_comment' => [
                'type' => 'text',
                'null' => true,
            ],
        ]);
        
        $this->dbforge->add_column('task_task_set_rel', [
            'internal_comment' => [
                'type' => 'text',
                'null' => true,
            ],
        ]);
    }
    
    public function down()
    {
        $this->dbforge->drop_column('task_sets', 'internal_comment');
        $this->dbforge->drop_column('tasks', 'internal_comment');
        $this->dbforge->drop_column('task_task_set_rel', 'internal_comment');
    }
    
}
