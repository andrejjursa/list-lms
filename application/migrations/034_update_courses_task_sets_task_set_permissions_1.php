<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_update_courses_task_sets_task_set_permissions_1 extends CI_Migration {

    public function up() {
        $this->dbforge->add_column('courses', array(
            'hide_in_lists' => array(
                'type' => 'int',
                'unsigned' => TRUE,
                'constraint' => 1,
            ),
        ));

        $this->dbforge->add_column('task_sets', array(
            'deadline_notification_emails' => array(
                'type' => 'text',
                'null' => true,
            ),
            'deadline_notified' => array(
                'type' => 'int',
                'unsigned' => TRUE,
                'constraint' => 1,
            ),
            'deadline_notification_emails_handler' => array(
                'type' => 'int',
                'unsigned' => TRUE,
                'constraint' => 2,
            ),
        ));

        $this->dbforge->add_column('task_set_permissions', array(
            'deadline_notification_emails' => array(
                'type' => 'text',
                'null' => true,
            ),
            'deadline_notified' => array(
                'type' => 'int',
                'unsigned' => TRUE,
                'constraint' => 1,
            ),
            'deadline_notification_emails_handler' => array(
                'type' => 'int',
                'unsigned' => TRUE,
                'constraint' => 2,
            ),
        ));
    }

    public function down() {
        $this->dbforge->drop_column('courses', 'hide_in_lists');
        $this->dbforge->drop_column('task_sets', 'deadline_notification_emails');
        $this->dbforge->drop_column('task_sets', 'deadline_notified');
        $this->dbforge->drop_column('task_sets', 'deadline_notification_emails_handler');
        $this->dbforge->drop_column('task_set_permissions', 'deadline_notification_emails');
        $this->dbforge->drop_column('task_set_permissions', 'deadline_notified');
        $this->dbforge->drop_column('task_set_permissions', 'deadline_notification_emails_handler');
    }

}
