<?php

class Migration_create_test_scores extends CI_Migration
{
    
    public function up()
    {
        $this->dbforge->add_field(
            [
                'task_id'    => [
                    'type'       => 'int',
                    'unsigned'   => true,
                    'null'       => true,
                    'constraint' => 11,
                ],
                'student_id' => [
                    'type'       => 'int',
                    'unsigned'   => true,
                    'null'       => true,
                    'constraint' => 11,
                ],
                'token'      => [
                    'type'       => 'varchar',
                    'constraint' => 32,
                    'default'    => '',
                ],
                'updated'    => [
                    'type'    => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ],
                'score'      => [
                    'type'       => 'int',
                    'unsigned'   => true,
                    'constraint' => 4,
                    'default'    => 0,
                ],
                'test_type'  => [
                    'type'       => 'varchar',
                    'constraint' => 255,
                    'default'    => '',
                ],
            ]
        );
        
        //$this->dbforge->add_key('task_id', TRUE);
        //$this->dbforge->add_key('student_id', TRUE);
        //$this->dbforge->add_key('token', TRUE);
        
        $this->dbforge->create_table('test_scores');
        
        change_mysql_table_to_InnoDB('test_scores');
        
        $this->db->query('ALTER TABLE `test_scores` ADD UNIQUE `unique_test_key` (`task_id`, `student_id`, `token`);');
    }
    
    public function down()
    {
        $this->dbforge->drop_table('test_scores');
    }
    
}
