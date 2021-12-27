<?php

class Migration_update_test_queues_3 extends CI_Migration
{
    
    public function up()
    {
        $this->dbforge->add_column('tests_queue', [
            'single_test_exec_start' => [
                'type'    => 'timestamp',
                'default' => '1970-01-01 01:00:01',
            ],
            'restarts'               => [
                'type'       => 'int',
                'constrains' => 4,
                'default'    => 0,
            ],
        ]);
    }
    
    public function down()
    {
        $this->dbforge->drop_column('tests_queue', 'single_test_exec_start');
        $this->dbforge->drop_column('tests_queue', 'restarts');
    }
    
}
