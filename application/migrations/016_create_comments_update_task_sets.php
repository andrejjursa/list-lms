<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_create_comments_update_task_sets extends CI_Migration {
    
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
                    'default' => '1970-01-01 01:00:01',
                ),  
                'created' => array(
                    'type' => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
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
        
        $this->dbforge->add_column('task_sets', array(
            'comments_enabled' => array(
                'type' => 'int',
                'constraint' => 1,
                'unsigned' => TRUE,
                'default' => '1',
            ),
            'comments_moderated' => array(
                'type' => 'int',
                'constraint' => 1,
                'unsigned' => TRUE,
                'default' => '0',
            ),
        ));
        
        $this->dbforge->add_field(
            array(
                'comment_subscription_id' => array(
                    'type' => 'int',
                    'constraint' => '11',
                    'unsigned' => TRUE,
                    'null' => TRUE,
                ),
                'comment_subscriber_student_id' => array(
                    'type' => 'int',
                    'constraint' => '11',
                    'unsigned' => TRUE,
                    'null' => TRUE,
                ),
                'comment_subscriber_teacher_id' => array(
                    'type' => 'int',
                    'constraint' => '11',
                    'unsigned' => TRUE,
                    'null' => TRUE,
                ),
            )
        );
        
        $this->dbforge->add_key('comment_subscription_id');
        $this->dbforge->add_key('comment_subscriber_student_id');
        $this->dbforge->add_key('comment_subscriber_teacher_id');
        
        $this->dbforge->create_table('task_set_comment_subscription_rel');
        
        change_mysql_table_to_InnoDB('task_set_comment_subscription_rel');
    }
    
    public function down() {
        $this->dbforge->drop_table('comments');
        $this->dbforge->drop_column('task_sets', 'comments_enabled');
        $this->dbforge->drop_column('task_sets', 'comments_moderated');
        $this->dbforge->drop_table('task_set_comment_subscription_rel');
    }
    
}
