<?php

class Migration_tables_for_projects extends CI_Migration
{
    
    public function up()
    {
        $this->dbforge->add_column('task_sets', [
            'project_selection_deadline' => [
                'type' => 'timestamp',
                'null' => true,
            ],
        ]);
        $this->db->query(
            'ALTER TABLE `task_sets` ADD `content_type` ENUM(\'task_set\', \'project\') '
            . 'NOT NULL DEFAULT \'task_set\' AFTER `created`;'
        );
        
        $this->dbforge->add_column('task_task_set_rel', [
            'max_projects_selections' => [
                'type'       => 'int',
                'constraint' => 5,
                'null'       => true,
                'unsigned'   => true,
            ],
        ]);
        
        $this->dbforge->add_field(
            [
                'id'          => [
                    'type'           => 'INT',
                    'constraint'     => '11',
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'updated'     => [
                    'type'    => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ],
                'created'     => [
                    'type'    => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ],
                'student_id'  => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'task_set_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'task_id'     => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
            ]
        );
        
        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key('student_id');
        $this->dbforge->add_key('task_set_id');
        $this->dbforge->add_key('task_id');
        
        $this->dbforge->create_table('project_selections');
        
        change_mysql_table_to_InnoDB('project_selections');
    }
    
    public function down()
    {
        $this->dbforge->drop_column('task_sets', 'project_selection_deadline');
        $this->dbforge->drop_column('task_sets', 'content_type');
        $this->dbforge->drop_column('task_task_set_rel', 'max_projects_selections');
        $this->dbforge->drop_table('project_selections');
    }
    
}