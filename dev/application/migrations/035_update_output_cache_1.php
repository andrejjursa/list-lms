<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_update_output_cache_1 extends CI_Migration {
    
    public function up() {
        $this->dbforge->add_column('output_cache', array(
            'list_version' => array(
                'type' => 'varchar',
                'constraint' => 64,
            ),
        ));
    }
    
    public function down() {
        $this->dbforge->drop_column('output_cache', 'list_version');
    }
    
}