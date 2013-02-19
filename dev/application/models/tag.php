<?php

class Tag extends DataMapper {
    
  var $has_many = array(
    'post' => array(
        'join_table' => 'posts_tags'
    ),
  );
    
}

?>