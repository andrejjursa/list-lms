<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_update_solutions_2 extends CI_Migration {

    public function up() {
        $this->dbforge->add_column('solutions', array(
            'disable_evaluation_by_tests' => array(
                'type' => 'int',
                'constraint' => '1',
                'default' => '0',
            ),
        ));
    }

    public function down() {
        $this->dbforge->drop_column('solutions', 'disable_evaluation_by_tests');
    }

}