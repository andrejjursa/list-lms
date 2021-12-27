<?php

class Migration_update_tests_and_test_queues extends CI_Migration
{
    
    public function up()
    {
        $this->dbforge->modify_column('tests', [
            'timeout' => [
                'type'       => 'int',
                'unsigned'   => true,
                'constraint' => 11,
                'default'    => 15000,
            ],
        ]);
        
        $this->dbforge->modify_column('test_test_queue_rel', [
            'result_text' => [
                'type' => 'mediumtext',
                'null' => true,
            ],
        ]);
    }
    
    public function down()
    {
        $this->dbforge->modify_column('tests', [
            'timeout' => [
                'type'       => 'int',
                'unsigned'   => true,
                'constraint' => 11,
                'default'    => 90000,
            ],
        ]);
        
        $this->dbforge->modify_column('test_test_queue_rel', [
            'result_text' => [
                'type' => 'text',
                'null' => true,
            ],
        ]);
    }
    
}