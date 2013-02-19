<?php

class Post extends DataMapper {
    
    var $has_many = array(
        'comment',
        'tag' => array(
            'join_table' => 'posts_tags'
        ),
    );
    
}

?>