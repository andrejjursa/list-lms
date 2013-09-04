<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_update_solutions_1 extends CI_Migration {
    
    public function up() {
        $this->dbforge->add_column('solutions', array(
            'not_considered' => array(
                'type' => 'int',
                'constraint' => '1',
            ),
        ));
    }
    
    public function down() {
        $this->dbforge->drop_column('solutions', 'not_considered');
    }
    
}