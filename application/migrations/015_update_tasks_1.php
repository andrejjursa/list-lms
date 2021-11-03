<?php

class Migration_update_tasks_1 extends CI_Migration
{
    
    public function up()
    {
        $this->dbforge->add_column('tasks', [
            'author_id' => [
                'type'       => 'int',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
        ]);
        
        $this->db->query('ALTER TABLE `tasks` ADD INDEX `author_id` ( `author_id` )');
    }
    
    public function down()
    {
        $this->dbforge->drop_column('tasks', 'author_id');
    }
    
}
