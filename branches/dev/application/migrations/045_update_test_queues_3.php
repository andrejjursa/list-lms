<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_update_test_queues_3 extends CI_Migration {

    public function up() {
        $this->dbforge->add_column('tests_queue', array(
            'single_test_exec_start' => array(
                'type' => 'timestamp',
                'default' => '0000-00-00 00:00:00',
            ),
            'restarts' => array(
                'type' => 'int',
                'constrains' => 4,
                'default' => 0,
            ),
        ));
    }

    public function down() {
        $this->dbforge->drop_column('tests_queue', 'single_test_exec_start');
        $this->dbforge->drop_column('tests_queue', 'restarts');
    }

}
