<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_tables_for_projects extends CI_Migration {
    
    public function up() {
        $this->dbforge->add_column('task_sets', array(
            'project_selection_deadline' => array(
                'type' => 'timestamp',
                'null' => TRUE,
            ),
        ));
        $this->db->query('ALTER TABLE `task_sets` ADD `content_type` ENUM(\'task_set\', \'project\') NOT NULL DEFAULT \'task_set\' AFTER `created`;');
        
        $this->dbforge->add_column('task_task_set_rel', array(
            'max_projects_selections' => array(
                'type' => 'int',
                'constraint' => 5,
                'null' => TRUE,
                'unsigned' => TRUE,
            ),
        ));
        
        $this->dbforge->add_field(
            array(
                'id' => array(
                    'type' => 'INT',
                    'constraint' => '11',
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE,
                ), 
                'updated' => array(
                    'type' => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ),  
                'created' => array(
                    'type' => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ),
                'student_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'null' => TRUE,
                ),
                'task_set_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'null' => TRUE,
                ),
                'task_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'null' => TRUE,
                ),
            )
        );
        
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('student_id');
        $this->dbforge->add_key('task_set_id');
        $this->dbforge->add_key('task_id');
        
        $this->dbforge->create_table('project_selections');
        
        change_mysql_table_to_InnoDB('project_selections');
    }
    
    public function down() {
        $this->dbforge->drop_column('task_sets', 'project_selection_deadline');
        $this->dbforge->drop_column('task_sets', 'content_type');
        $this->dbforge->drop_column('task_task_set_rel', 'max_projects_selections');
        $this->dbforge->drop_table('project_selections');
    }
    
}