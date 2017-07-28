<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_create_lang_overlays extends CI_Migration {

    public function up() {
        $this->dbforge->add_field(
            array(
                'table' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 128,
                    'default' => '',
                ),
                'table_id' => array(
                    'type' => 'INT',
                    'constraint' => '11',
                    'default' => 0,
                ),
                'idiom' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 32,
                    'default' => '',
                ),
                'column' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 128,
                    'default' => '',
                ),
                'text' => array(
                    'type' => 'TEXT',
                    'null' => true,
                ),
            )
        );

        $this->dbforge->add_key('table', TRUE);
        $this->dbforge->add_key('table_id', TRUE);
        $this->dbforge->add_key('idiom', TRUE);
        $this->dbforge->add_key('column', TRUE);

        $this->dbforge->create_table('lang_overlays');
    }

    public function down() {
        $this->dbforge->drop_table('lang_overlays');
    }

}
