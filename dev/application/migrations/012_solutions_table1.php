<?php

class Migration_Solutions_table1 extends CI_Migration {
    
    public function up() {
        
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
                'auto_increment' => TRUE,
            ), 
            'updated' => array(
                'type' => 'timestamp',
            ),  
            'created' => array(
                'type' => 'timestamp',
            ),
            'task_set_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
                'null' => TRUE,
            ),
            'student_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
                'null' => TRUE,
            ),
            'teacher_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
                'null' => TRUE,
            ),
            'comment' => array(
                'type' => 'TEXT',
            ),
            'points' => array(
                'type' => 'double',
            ),
            'revalidate' => array(
                'type' => 'int',
                'constraint' => '1',
            ),
        ));
        
        $this->dbforge->add_key('id', TRUE);
        
        $this->dbforge->create_table('solutions');
        
        change_mysql_table_to_InnoDB('solutions');
        
    }
    
    public function down() {
        $this->dbforge->drop_table('solutions');
    }
    
}