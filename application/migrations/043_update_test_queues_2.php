<?php

class Migration_update_test_queues_2 extends CI_Migration
{
    
    public function up()
    {
        $this->dbforge->add_column('test_test_queue_rel', [
            'evaluation_table' => [
                'type' => 'text',
                'null' => true,
            ],
        ]);
    }
    
    public function down()
    {
        $this->dbforge->drop_column('test_test_queue_rel', 'evaluation_table');
    }
    
}
