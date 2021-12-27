<?php

class Migration_update_task_sets_2 extends CI_Migration
{
    
    public function up()
    {
        $this->dbforge->add_column('task_sets', [
            'allowed_file_types' => [
                'type' => 'text',
                'null' => true,
            ],
        ]);
    }
    
    public function down()
    {
        $this->dbforge->drop_column('task_sets', 'allowed_file_types');
    }
    
}
