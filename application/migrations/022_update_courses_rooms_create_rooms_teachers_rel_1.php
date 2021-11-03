<?php

class Migration_update_courses_rooms_create_rooms_teachers_rel_1 extends CI_Migration
{
    
    public function up()
    {
        $this->dbforge->add_column('courses', [
            'allow_subscription_to' => [
                'type' => 'timestamp',
                'null' => true,
            ],
        ]);
        
        $this->dbforge->add_column('rooms', [
            'teachers_plain' => [
                'type' => 'text',
                'null' => true,
            ],
        ]);
        
        $this->dbforge->add_field(
            [
                'room_id'    => [
                    'type'       => 'INT',
                    'constraint' => '11',
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'teacher_id' => [
                    'type'       => 'INT',
                    'constraint' => '11',
                    'unsigned'   => true,
                    'null'       => true,
                ],
            ]
        );
        
        $this->dbforge->add_key('room_id');
        $this->dbforge->add_key('teacher_id');
        
        $this->dbforge->create_table('rooms_teachers_rel');
        
        change_mysql_table_to_InnoDB('rooms_teachers_rel');
    }
    
    public function down()
    {
        $this->dbforge->drop_column('courses', 'allow_subscription_to');
        $this->dbforge->drop_column('rooms', 'teachers_plain');
        $this->dbforge->drop_table('rooms_teachers_rel');
    }
    
}