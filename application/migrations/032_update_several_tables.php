<?php

class Migration_update_several_tables extends CI_Migration
{
    
    public function up()
    {
        $this->dbforge->add_column('courses', [
            'test_scoring_deadline' => [
                'type'    => 'timestamp',
                'default' => '1970-01-01 01:00:01',
            ],
        ]);
        
        $this->dbforge->add_column('task_sets', [
            'enable_tests_scoring' => [
                'type'       => 'int',
                'unsigned'   => true,
                'constraint' => 1,
                'default'    => 0,
            ],
        ]);
        
        $this->dbforge->add_column('task_task_set_rel', [
            'test_min_points' => [
                'type'    => 'double',
                'default' => 0,
            ],
            'test_max_points' => [
                'type'    => 'double',
                'default' => 0,
            ],
        ]);
        
        $this->dbforge->add_column('solutions', [
            'best_version' => [
                'type'       => 'int',
                'unsigned'   => true,
                'constraint' => 6,
                'default'    => 0,
            ],
        ]);
    }
    
    public function down()
    {
        $this->dbforge->drop_column('courses', 'test_scoring_deadline');
        $this->dbforge->drop_column('task_sets', 'enable_tests_scoring');
        $this->dbforge->drop_column('task_task_set_rel', 'test_min_points');
        $this->dbforge->drop_column('task_task_set_rel', 'test_max_points');
        $this->dbforge->drop_column('solutions', 'best_version');
    }
    
}
