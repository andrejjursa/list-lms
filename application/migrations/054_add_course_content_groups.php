<?php

class Migration_add_course_content_groups extends CI_Migration {
    
    public function up() {
        $this->dbforge->add_field(
            [
                'id' => [
                    'type' => 'INT',
                    'constraint' => '11',
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE,
                ],
                'updated' => [
                    'type' => 'TIMESTAMP',
                    'default' => '1970-01-01 01:00:01',
                ],
                'created' => [
                    'type' => 'TIMESTAMP',
                    'default' => '1970-01-01 01:00:01',
                ],
                'title' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'default' => '',
                ],
                'course_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'null' => TRUE,
                ],
            ]
        );
        
        $this->dbforge->add_key('id', TRUE);
        
        $this->dbforge->add_key('course_id');
        
        $this->dbforge->create_table('course_content_groups');
        
        change_mysql_table_to_InnoDB('course_content_groups');
    }
    
    public function down() {
        $this->dbforge->drop_table('course_content_groups');
    }
    
}
