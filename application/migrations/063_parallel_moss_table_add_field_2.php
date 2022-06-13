<?php

/**
 * @property CI_DB_forge         $dbforge
 * @property CI_DB_active_record $db
 */
class Migration_parallel_moss_table_add_field_2 extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_column(
            'parallel_moss_comparisons',
            [
                'comparison_name' => [
                    'type' => 'varchar',
                    'constraint' => 255,
                    'null' => true,
                ],
            ],
            'id'
        );
    }
    
    public function down()
    {
        $this->dbforge->drop_column('parallel_moss_comparisons', 'comparison_name');
    }
}