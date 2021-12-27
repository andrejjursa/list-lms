<?php

class Migration_create_output_cache extends CI_Migration
{
    
    public function up()
    {
        $this->db->query('CREATE TABLE IF NOT EXISTS `output_cache` (
`id` CHAR(40) NOT NULL COMMENT \'sha1 hash\',
`name` VARCHAR(250) NOT NULL DEFAULT "",
`cache_id` VARCHAR(250) NULL DEFAULT NULL,
`compile_id` VARCHAR(250) NULL DEFAULT NULL,
`modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
`content` LONGTEXT NULL DEFAULT NULL,
PRIMARY KEY (`id`),
INDEX(`name`),
INDEX(`cache_id`),
INDEX(`compile_id`),
INDEX(`modified`)
) ENGINE = InnoDB, COLLATE = utf8_general_ci;');
    }
    
    public function down()
    {
        $this->dbforge->drop_table('output_cache');
    }
    
}
