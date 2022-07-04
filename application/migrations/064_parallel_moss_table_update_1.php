<?php

/**
 * @property CI_DB_forge $dbforge
 * @property CI_DB_active_record $db
 */
class Migration_parallel_moss_table_update_1 extends CI_Migration
{
    public function up()
    {
        $this->db->query(
            'ALTER TABLE `parallel_moss_comparisons` CHANGE `status` `status` ENUM(\'queued\', \'processing\', \'finished\', \'failed\', \'restart\')'
            . 'NOT NULL DEFAULT \'queued\' AFTER `created`;'
        );
        
        $this->dbforge->add_column(
            'parallel_moss_comparisons',
            [
                'restarts' => [
                    'type' => 'int',
                    'unsigned' => true,
                    'null' => true,
                ],
            ],
            'status'
        );
    }
    
    public function down()
    {
        $this->db->query(
            'ALTER TABLE `parallel_moss_comparisons` CHANGE `status` `status` ENUM(\'queued\', \'processing\', \'finished\', \'failed\')'
            . 'NOT NULL DEFAULT \'queued\' AFTER `created`;'
        );
        
        $this->dbforge->drop_column('parallel_moss_comparisons', 'restarts');
    }
}