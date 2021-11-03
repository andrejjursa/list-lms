<?php

class Migration_update_task_sets_sorting extends CI_Migration
{
    
    public function up()
    {
        $this->dbforge->add_column('task_sets', [
            'sorting' => [
                'type'       => 'int',
                'unsigned'   => true,
                'constraint' => 6,
                'default'    => 0,
            ],
        ]);
    }
    
    public function down()
    {
        $this->dbforge->drop_column('task_sets', 'sorting');
    }
    
}
