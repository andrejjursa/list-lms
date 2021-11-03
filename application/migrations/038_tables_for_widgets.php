<?php

class Migration_tables_for_widgets extends CI_Migration
{
    
    public function up()
    {
        $this->dbforge->add_field(
            [
                'id'            => [
                    'type'           => 'INT',
                    'constraint'     => '11',
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'updated'       => [
                    'type'    => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ],
                'created'       => [
                    'type'    => 'timestamp',
                    'default' => '1970-01-01 01:00:01',
                ],
                'teacher_id'    => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'widget_type'   => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'default'    => '',
                ],
                'widget_config' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'position'      => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'default'    => 0,
                ],
                'column'        => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'default'    => 1,
                ],
            ]
        );
        
        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key('teacher_id');
        
        $this->dbforge->create_table('admin_widgets');
        
        change_mysql_table_to_InnoDB('admin_widgets');
        
        $this->dbforge->add_column('teachers', [
            'widget_columns' => [
                'type'       => 'INT',
                'constraint' => 4,
                'unsigned'   => true,
                'default'    => '1',
            ],
        ]);
    }
    
    public function down()
    {
        $this->dbforge->drop_column('teachers', 'widget_columns');
        $this->dbforge->drop_table('admin_widgets');
    }
    
}
