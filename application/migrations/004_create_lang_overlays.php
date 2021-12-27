<?php

class Migration_create_lang_overlays extends CI_Migration
{
    
    public function up()
    {
        $this->dbforge->add_field(
            [
                'table'    => [
                    'type'       => 'VARCHAR',
                    'constraint' => 128,
                    'default'    => '',
                ],
                'table_id' => [
                    'type'       => 'INT',
                    'constraint' => '11',
                    'default'    => 0,
                ],
                'idiom'    => [
                    'type'       => 'VARCHAR',
                    'constraint' => 32,
                    'default'    => '',
                ],
                'column'   => [
                    'type'       => 'VARCHAR',
                    'constraint' => 128,
                    'default'    => '',
                ],
                'text'     => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
            ]
        );
        
        $this->dbforge->add_key('table', true);
        $this->dbforge->add_key('table_id', true);
        $this->dbforge->add_key('idiom', true);
        $this->dbforge->add_key('column', true);
        
        $this->dbforge->create_table('lang_overlays');
    }
    
    public function down()
    {
        $this->dbforge->drop_table('lang_overlays');
    }
    
}
