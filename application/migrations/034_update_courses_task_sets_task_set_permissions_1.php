<?php

class Migration_update_courses_task_sets_task_set_permissions_1 extends CI_Migration
{
    
    public function up()
    {
        $this->dbforge->add_column('courses', [
            'hide_in_lists' => [
                'type'       => 'int',
                'unsigned'   => true,
                'constraint' => 1,
                'default'    => 0,
            ],
        ]);
        
        $this->dbforge->add_column('task_sets', [
            'deadline_notification_emails'         => [
                'type' => 'text',
                'null' => true,
            ],
            'deadline_notified'                    => [
                'type'       => 'int',
                'unsigned'   => true,
                'constraint' => 1,
                'default'    => 0,
            ],
            'deadline_notification_emails_handler' => [
                'type'       => 'int',
                'unsigned'   => true,
                'constraint' => 2,
                'default'    => 0,
            ],
        ]);
        
        $this->dbforge->add_column('task_set_permissions', [
            'deadline_notification_emails'         => [
                'type' => 'text',
                'null' => true,
            ],
            'deadline_notified'                    => [
                'type'       => 'int',
                'unsigned'   => true,
                'constraint' => 1,
                'default'    => 0,
            ],
            'deadline_notification_emails_handler' => [
                'type'       => 'int',
                'unsigned'   => true,
                'constraint' => 2,
                'default'    => 0,
            ],
        ]);
    }
    
    public function down()
    {
        $this->dbforge->drop_column('courses', 'hide_in_lists');
        $this->dbforge->drop_column('task_sets', 'deadline_notification_emails');
        $this->dbforge->drop_column('task_sets', 'deadline_notified');
        $this->dbforge->drop_column('task_sets', 'deadline_notification_emails_handler');
        $this->dbforge->drop_column('task_set_permissions', 'deadline_notification_emails');
        $this->dbforge->drop_column('task_set_permissions', 'deadline_notified');
        $this->dbforge->drop_column('task_set_permissions', 'deadline_notification_emails_handler');
    }
    
}
