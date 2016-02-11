<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Migration_update_courses_2 extends CI_Migration {

		public function up() {
			$this->dbforge->add_column('courses', array(
				'auto_accept_students' => array(
					'type' => 'int',
					'default' => '0',
					'constraint' => TRUE,
				),
			));
		}

		public function down() {
			$this->dbforge->drop_column('courses', 'auto_accept_students');
		}

	}
