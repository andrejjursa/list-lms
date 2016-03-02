<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_update_solutions_3 extends CI_Migration {

    public function up() {
        $this->db->query('ALTER TABLE `solutions` CHANGE `points` `tests_points` DOUBLE NULL DEFAULT NULL;');

        $this->dbforge->add_column('solutions', array(
            'points' => array(
                'type' => 'double',
                'null' => TRUE,
            ),
        ));
    }

    public function down() {
        $this->dbforge->drop_column('solutions', 'points');

        $this->db->query('ALTER TABLE `solutions` CHANGE `tests_points` `points` DOUBLE NULL DEFAULT NULL;');
    }

}
