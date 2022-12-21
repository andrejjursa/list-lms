<?php

namespace Application\Services\Formula\Node;

use Application\Exceptions\FormulaException\NotImplementedException;

class Formula implements Formula_node
{
    public $left, $right;
    
    public function __construct(Formula_node $left, Formula_node $right) {
        $this->left = $left;
        $this->right = $right;
    }
    
    public function evaluate(): float
    {
        throw new NotImplementedException("Method not implemented");
    }
    
    public function toString(): string
    {
        throw new NotImplementedException("Method not implemented");
    }
}