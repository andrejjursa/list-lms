<?php

/**
 * @property CI_DB_forge         $dbforge
 * @property CI_DB_active_record $db
 */

class Migration_formulas extends CI_Migration
{
    public function up()
    {
        $this->db->query('ALTER TABLE  `course_task_set_type_rel` ADD  `min_points` DOUBLE NULL DEFAULT NULL');
        $this->dbforge->add_column('course_task_set_type_rel', [
            'min_points_in_percentage' => [
                'type'       => 'tinyint',
                'constraint' => '1',
                'unsigned'   => true,
                'default'    => 1,
            ],
            'include_in_total' => [
                'type'       => 'tinyint',
                'constraint' => '1',
                'unsigned'   => true,
                'default'    => 1,
            ],
            'virtual' => [
                'type'       => 'tinyint',
                'constraint' => '1',
                'unsigned'   => true,
                'default'    => 0,
            ],
            'formula' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'formula_object' => [
                'type' => 'TEXT',
                'null' => true,
            ]
        ]);
    }
    
    public function down() {
        $this->dbforge->drop_column('course_task_set_type_rel', 'min_points');
        $this->dbforge->drop_column('course_task_set_type_rel', 'min_points_in_percentage');
        $this->dbforge->drop_column('course_task_set_type_rel', 'include_in_total');
        $this->dbforge->drop_column('course_task_set_type_rel', 'virtual');
        $this->dbforge->drop_column('course_task_set_type_rel', 'formula');
        $this->dbforge->drop_column('course_task_set_type_rel', 'formula_object');
    }
}