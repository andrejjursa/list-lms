<?php

class Migration_update_solutions_1 extends CI_Migration
{
    
    public function up()
    {
        $this->dbforge->add_column('solutions', [
            'not_considered' => [
                'type'       => 'int',
                'constraint' => '1',
                'default'    => 0,
            ],
        ]);
    }
    
    public function down()
    {
        $this->dbforge->drop_column('solutions', 'not_considered');
    }
    
}
