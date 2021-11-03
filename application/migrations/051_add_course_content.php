<?php

class Migration_add_course_content extends CI_Migration
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
                    'type'    => 'TIMESTAMP',
                    'default' => '1970-01-01 01:00:01',
                ],
                'created'     => [
                    'type'    => 'TIMESTAMP',
                    'default' => '1970-01-01 01:00:01',
                ],
                'title'       => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'default'    => '',
                ],
                'content'     => [
                    'type' => 'TEXT',
                    'null' => false,
                ],
                'course_id'   => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'published'   => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'null'       => false,
                    'default'    => 0,
                ],
                'permissions' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
            ]
        );
        
        $this->dbforge->add_key('id', true);
        
        $this->dbforge->add_key('course_id');
        
        $this->dbforge->create_table('course_content');
        
        change_mysql_table_to_InnoDB('course_content');
    }
    
    public function down()
    {
        $this->dbforge->drop_table('course_content');
    }
    
}
