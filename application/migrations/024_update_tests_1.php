<?php

class Migration_update_tests_1 extends CI_Migration
{
    
    public function up()
    {
        $this->dbforge->add_column('tests', [
            'instructions' => [
                'type' => 'text',
                'null' => true,
            ],
        ]);
        
        $this->db->simple_query('ALTER TABLE  `tests` CHANGE  `configuration`  `configuration` LONGTEXT NULL DEFAULT NULL;');
    }
    
    public function down()
    {
        $this->dbforge->drop_column('tests', 'instructions');
        
        $this->db->simple_query('ALTER TABLE  `tests` CHANGE  `configuration`  `configuration` TEXT NULL DEFAULT NULL;');
    }
    
}
