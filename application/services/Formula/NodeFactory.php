<?php

namespace Application\Services\Formula;

use Application\Services\Formula\Node\Addition;
use Application\Services\Formula\Node\Formula_node;

class NodeFactory
{
    public function getAddition(Formula_node $left, Formula_node $right):Addition
    {
        return new Addition($left, $right);
    }
    
    // TODO add all other classes
}