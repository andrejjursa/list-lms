<?php

class Migration_update_tests_and_test_queues extends CI_Migration {

    public function up() {
        $this->dbforge->modify_column('tests', array(
            'timeout' => array(
                'type' => 'int',
                'unsigned' => TRUE,
                'constraint' => 11,
                'default' => 15000,
            ),
        ));

        $this->dbforge->modify_column('test_test_queue_rel', array(
            'result_text' => array(
                'type' => 'mediumtext',
                'null' => TRUE,
            ),
        ));
    }

    public function down() {
        $this->dbforge->modify_column('tests', array(
            'timeout' => array(
                'type' => 'int',
                'unsigned' => TRUE,
                'constraint' => 11,
                'default' => 90000,
            ),
        ));

        $this->dbforge->modify_column('test_test_queue_rel', array(
            'result_text' => array(
                'type' => 'text',
                'null' => TRUE,
            ),
        ));
    }

}