<?php

class Migration_update_task_sets_4 extends CI_Migration
{
    
    public function up()
    {
        $this->dbforge->add_column('task_sets', [
            'test_min_needed'  => [
                'type'       => 'int',
                'unsigned'   => true,
                'constraint' => 6,
                'default'    => 0,
            ],
            'test_max_allowed' => [
                'type'       => 'int',
                'unsigned'   => true,
                'constraint' => 6,
                'default'    => 0,
            ],
        ]);
    }
    
    public function down()
    {
        $this->dbforge->drop_column('task_sets', 'test_min_needed');
        $this->dbforge->drop_column('task_sets', 'test_max_allowed');
    }
    
}
