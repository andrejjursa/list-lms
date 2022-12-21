<?php

namespace Application\Services\Formula\Node;

use Application\Services\Formula\Node\Formula;
use Application\Services\Formula\Node\Formula_node;

class Multiplication implements Formula
{
    private $left;
    private $right;
    
    public function __construct(Formula_node $left, Formula_node $right)
    {
        $this->left = $left;
        $this->right = $right;
    }
    
    public function get_left(): Formula_node
    {
        return $this->left;
    }
    
    public function get_right(): Formula_node
    {
        return $this->right;
    }
    
    public function compute(): float
    {
        // TODO: Implement compute() method.
        return 0;
    }
    
    public function evaluate(): bool
    {
        // TODO: Implement evaluate() method.
        return true;
    }
    
    public function toString(): string
    {
        return "( " . $this->get_left()->toString() . " * " . $this->get_right()->toString() . " )";
    }
}