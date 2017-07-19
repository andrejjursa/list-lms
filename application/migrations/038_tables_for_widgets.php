<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_tables_for_widgets extends CI_Migration {
    
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
                'teacher_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'null' => TRUE,
                ),
                'widget_type' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                ),
                'widget_config' => array(
                    'type' => 'TEXT',
                ),
                'position' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                ),
                'column' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                ),
            )
        );
        
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('teacher_id');
        
        $this->dbforge->create_table('admin_widgets');
        
        change_mysql_table_to_InnoDB('admin_widgets');
        
        $this->dbforge->add_column('teachers', array(
            'widget_columns' => array(
                'type' => 'INT',
                'constraint' => 4,
                'unsigned' => TRUE,
                'default' => '1',
            ),
        ));
    }
    
    public function down() {
        $this->dbforge->drop_column('teachers', 'widget_columns');
        $this->dbforge->drop_table('admin_widgets');
    }
    
}