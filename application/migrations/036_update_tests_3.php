<?php

class Migration_update_tests_3 extends CI_Migration
{
    
    public function up()
    {
        $this->dbforge->add_column('tests', [
            'timeout' => [
                'type'       => 'int',
                'unsigned'   => true,
                'constraint' => 11,
                'default'    => 90000,
            ],
        ]);
    }
    
    public function down()
    {
        $this->dbforge->drop_column('tests', 'timeout');
    }
    
}