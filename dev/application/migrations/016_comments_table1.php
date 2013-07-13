<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_comments_table1 extends CI_Migration {
    
    public function up() {
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
                ),  
                'created' => array(
                    'type' => 'timestamp',
                ),
                'text' => array(
                    'type' => 'text'
                ),
                'task_set_id' => array(
                    'type' => 'int',
                    'constraint' => '11',
                    'unsigned' => TRUE,
                    'null' => TRUE,
                ),
                'reply_at_id' => array(
                    'type' => 'int',
                    'constraint' => '11',
                    'unsigned' => TRUE,
                    'null' => TRUE,
                ),
                'student_id' => array(
                    'type' => 'int',
                    'constraint' => '11',
                    'unsigned' => TRUE,
                    'null' => TRUE,
                ),
                'teacher_id' => array(
                    'type' => 'int',
                    'constraint' => '11',
                    'unsigned' => TRUE,
                    'null' => TRUE,
                ),
                'approved' => array(
                    'type' => 'int',
                    'constraint' => '1',
                    'unsigned' => TRUE,
                ),
            )
        );
        
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('task_set_id');
        $this->dbforge->add_key('reply_at_id');
        $this->dbforge->add_key('student_id');
        $this->dbforge->add_key('teacher_id');
        
        $this->dbforge->create_table('comments');
        
        change_mysql_table_to_InnoDB('comments');
    }
    
    public function down() {
        $this->dbforge->drop_table('comments');
    }
    
}
