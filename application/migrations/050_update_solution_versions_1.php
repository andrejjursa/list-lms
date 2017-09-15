<?php

class Migration_update_solution_versions_1 extends CI_Migration {

  public function up() {
    $this->dbforge->add_column('solution_versions', [
      'comment' => [
        'type' => 'TEXT',
        'null' => true,
      ]
    ]);
  }

  public function down() {
    $this->dbforge->drop_column('solution_versions', 'comment');
  }

}
