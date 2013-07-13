<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_tasks_table2 extends CI_Migration {
    
    public function up() {
        $this->dbforge->add_column('tasks', array(
            'author_id' => array(
                'type' => 'int',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => TRUE,
            ),
        ));
        
        $this->db->query('ALTER TABLE `tasks` ADD INDEX `author_id` ( `author_id` )');
    }
    
    public function down() {
        $this->dbforge->drop_column('tasks', 'author_id');
    }
    
}
