<?php

namespace Application\Services\Formula\Node;

/**
 * @property Formula_node $left
 * @property Formula_node $right
 */

interface Formula extends Formula_node
{
    public function get_left();
    public function get_right();
}