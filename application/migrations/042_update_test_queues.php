<?php

class Migration_update_test_queues extends CI_Migration {
    
    public function up() {
        $this->dbforge->add_column('tests_queue', array(
            'age' => array(
                'type' => 'int',
                'constraint' => '11',
                'unsigned' => TRUE,
                'default' => 0,
            ),
            'result_html' => array(
                'type' => 'text',
				'null' => true,
            ),
            'result_message' => array(
                'type' => 'text',
				'null' => true,
            ),
        ));
    }
    
    public function down() {
        $this->dbforge->drop_column('tests_queue', 'age');
        $this->dbforge->drop_column('tests_queue', 'result_html');
        $this->dbforge->drop_column('tests_queue', 'result_message');
    }
    
}
