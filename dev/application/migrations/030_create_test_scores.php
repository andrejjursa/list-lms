<?php

class Migration_create_test_scores extends CI_Migration {

    public function up() {
        $this->dbforge->add_field(
            array(
                'task_id' => array(
                    'type' => 'int',
                    'unsigned' => TRUE,
                    'null' => TRUE,
                    'constraint' => 11,
                ),
                'student_id' => array(
                    'type' => 'int',
                    'unsigned' => TRUE,
                    'null' => TRUE,
                    'constraint' => 11,
                ),
                'token' => array(
                    'type' => 'varchar',
                    'constraint' => 32,
                ),
                'updated' => array(
                    'type' => 'timestamp',
                ),
                'score' => array(
                    'type' => 'int',
                    'unsigned' => TRUE,
                    'constraint' => 4,
                ),
                'test_type' => array(
                    'type' => 'varchar',
                    'constraint' => 255,
                ),
            )
        );
        
        $this->dbforge->add_key('test_id', TRUE);
        $this->dbforge->add_key('student_id', TRUE);
        $this->dbforge->add_key('token', TRUE);
        
        $this->dbforge->create_table('test_scores');
        
        change_mysql_table_to_InnoDB('test_scores');
    }
    
    public function down() {
        $this->dbforge->drop_table('test_scores');
    }
    
}
