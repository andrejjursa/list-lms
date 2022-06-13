<?php

/**
 * @property CI_DB_forge         $dbforge
 * @property CI_DB_active_record $db
 */
class Migration_parallel_moss_tables extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field(
            [
                'id'                => [
                    'type'           => 'INT',
                    'constraint'     => '11',
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'updated'           => [
                    'type'    => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ],
                'created'           => [
                    'type'    => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ],
                'teacher_id'        => [
                    'type'       => 'INT',
                    'constraint' => '11',
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'configuration'     => [
                    'type' => 'TEXT',
                    'null' => false,
                ],
                'processing_start'  => [
                    'type' => 'timestamp',
                    'null' => true,
                ],
                'processing_finish' => [
                    'type' => 'timestamp',
                    'null' => true,
                ],
                'result_link'       => [
                    'type'       => 'varchar',
                    'constraint' => '255',
                    'null'       => true,
                ],
            ]
        );
        
        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key('teacher_id');
        
        $this->dbforge->create_table('parallel_moss_comparisons');
        
        change_mysql_table_to_InnoDB('parallel_moss_comparisons');
        
        $this->db->query(
            'ALTER TABLE `parallel_moss_comparisons` ADD `status` ENUM(\'queued\', \'processing\', \'finished\', \'failed\')'
            . 'NOT NULL DEFAULT \'queued\' AFTER `created`;'
        );
    }
    
    public function down()
    {
        $this->dbforge->drop_table('parallel_moss_comparisons');
    }
}