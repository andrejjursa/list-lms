<?php

class Migration_create_comments_update_task_sets extends CI_Migration
{
    
    public function up()
    {
        $this->dbforge->add_field(
            [
                'id'          => [
                    'type'           => 'INT',
                    'constraint'     => '11',
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'updated'     => [
                    'type'    => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ],
                'created'     => [
                    'type'    => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ],
                'text'        => [
                    'type' => 'text',
                    'null' => true,
                ],
                'task_set_id' => [
                    'type'       => 'int',
                    'constraint' => '11',
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'reply_at_id' => [
                    'type'       => 'int',
                    'constraint' => '11',
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'student_id'  => [
                    'type'       => 'int',
                    'constraint' => '11',
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'teacher_id'  => [
                    'type'       => 'int',
                    'constraint' => '11',
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'approved'    => [
                    'type'       => 'int',
                    'constraint' => '1',
                    'unsigned'   => true,
                    'default'    => 0,
                ],
            ]
        );
        
        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key('task_set_id');
        $this->dbforge->add_key('reply_at_id');
        $this->dbforge->add_key('student_id');
        $this->dbforge->add_key('teacher_id');
        
        $this->dbforge->create_table('comments');
        
        change_mysql_table_to_InnoDB('comments');
        
        $this->dbforge->add_column('task_sets', [
            'comments_enabled'   => [
                'type'       => 'int',
                'constraint' => 1,
                'unsigned'   => true,
                'default'    => '1',
            ],
            'comments_moderated' => [
                'type'       => 'int',
                'constraint' => 1,
                'unsigned'   => true,
                'default'    => '0',
            ],
        ]);
        
        $this->dbforge->add_field(
            [
                'comment_subscription_id'       => [
                    'type'       => 'int',
                    'constraint' => '11',
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'comment_subscriber_student_id' => [
                    'type'       => 'int',
                    'constraint' => '11',
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'comment_subscriber_teacher_id' => [
                    'type'       => 'int',
                    'constraint' => '11',
                    'unsigned'   => true,
                    'null'       => true,
                ],
            ]
        );
        
        $this->dbforge->add_key('comment_subscription_id');
        $this->dbforge->add_key('comment_subscriber_student_id');
        $this->dbforge->add_key('comment_subscriber_teacher_id');
        
        $this->dbforge->create_table('task_set_comment_subscription_rel');
        
        change_mysql_table_to_InnoDB('task_set_comment_subscription_rel');
    }
    
    public function down()
    {
        $this->dbforge->drop_table('comments');
        $this->dbforge->drop_column('task_sets', 'comments_enabled');
        $this->dbforge->drop_column('task_sets', 'comments_moderated');
        $this->dbforge->drop_table('task_set_comment_subscription_rel');
    }
    
}
