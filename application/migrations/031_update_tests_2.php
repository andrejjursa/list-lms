<?php

class Migration_update_tests_2 extends CI_Migration
{
    
    public function up()
    {
        $this->dbforge->add_column('tests', [
            'enable_scoring' => [
                'type'       => 'int',
                'unsigned'   => true,
                'constraint' => 1,
                'default'    => 0,
            ],
        ]);
    }
    
    public function down()
    {
        $this->dbforge->drop_column('tests', 'enable_scoring');
    }
    
}
