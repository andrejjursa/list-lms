<?php

/**
 * @property CI_DB_forge         $dbforge
 * @property CI_DB_active_record $db
 */

class Migration_add_task_set_type_identifier extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_column('task_set_types', [
            'identifier' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'      => true,
            ]
        ]);
        
        $this->db->query('ALTER TABLE  `task_set_types` ADD UNIQUE (identifier)');
    }
    
    public function down() {
        $this->dbforge->drop_column('task_set_types', 'identifier');
    }
}