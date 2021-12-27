<?php

class Migration_update_test_queues extends CI_Migration
{
    
    public function up()
    {
        $this->dbforge->add_column('tests_queue', [
            'age'            => [
                'type'       => 'int',
                'constraint' => '11',
                'unsigned'   => true,
                'default'    => 0,
            ],
            'result_html'    => [
                'type' => 'text',
                'null' => true,
            ],
            'result_message' => [
                'type' => 'text',
                'null' => true,
            ],
        ]);
    }
    
    public function down()
    {
        $this->dbforge->drop_column('tests_queue', 'age');
        $this->dbforge->drop_column('tests_queue', 'result_html');
        $this->dbforge->drop_column('tests_queue', 'result_message');
    }
    
}
