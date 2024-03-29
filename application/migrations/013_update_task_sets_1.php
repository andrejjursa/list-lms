<?php

class Migration_update_task_sets_1 extends CI_Migration
{
    
    public function up()
    {
        $this->dbforge->add_column('task_sets', [
            'instructions' => [
                'type' => 'text',
                'null' => true,
            ],
        ]);
        
        $this->db->query('ALTER TABLE  `task_sets` ADD  `points_override` DOUBLE NULL DEFAULT NULL');
    }
    
    public function down()
    {
        $this->dbforge->drop_column('task_sets', 'instructions');
        $this->dbforge->drop_column('task_sets', 'points_override');
    }
    
}
