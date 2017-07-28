<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_update_tests_2 extends CI_Migration {

    public function up() {
        $this->dbforge->add_column('tests', array(
            'enable_scoring' => array(
                'type' => 'int',
                'unsigned' => TRUE,
                'constraint' => 1,
                'default' => 0,
            ),
        ));
    }

    public function down() {
        $this->dbforge->drop_column('tests', 'enable_scoring');
    }

}
