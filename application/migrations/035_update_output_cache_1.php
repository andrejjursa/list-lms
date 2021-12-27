<?php

class Migration_update_output_cache_1 extends CI_Migration
{
    
    public function up()
    {
        $this->dbforge->add_column('output_cache', [
            'list_version' => [
                'type'       => 'varchar',
                'constraint' => 64,
                'default'    => '',
            ],
        ]);
    }
    
    public function down()
    {
        $this->dbforge->drop_column('output_cache', 'list_version');
    }
    
}
