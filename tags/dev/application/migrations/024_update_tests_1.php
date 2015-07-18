<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_update_tests_1 extends CI_Migration {
    
    public function up() {
        $this->dbforge->add_column('tests', array(
            'instructions' => array(
                'type' => 'text',
            ),
        ));
        
        $this->db->simple_query('ALTER TABLE  `tests` CHANGE  `configuration`  `configuration` LONGTEXT NOT NULL ;');
    }
    
    public function down() {
        $this->dbforge->drop_column('tests', 'instructions');
        
        $this->db->simple_query('ALTER TABLE  `tests` CHANGE  `configuration`  `configuration` TEXT NOT NULL ;');
    }
    
}