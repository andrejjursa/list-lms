<?php

class Comment extends DataMapper {
    
    var $has_one = array('post');
    
}

?>