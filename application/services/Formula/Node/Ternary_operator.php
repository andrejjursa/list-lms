<?php

namespace Application\Services\Formula\Node;

class Ternary_operator implements Formula
{
    private $left;
    private $right;
    private $condition;
    
    public function __construct(Formula_node $left, Formula_node $right, Formula_node $condition)
    {
        $this->left = $left;
        $this->right = $right;
        $this->condition = $condition;
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
        return "( ".$this->condition->toString() . " ? " . $this->left->toString() . " : " . $this->right->toString()." )";
    }
}