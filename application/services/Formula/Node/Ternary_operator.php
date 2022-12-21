<?php

namespace Application\Services\Formula\Node;

class Ternary_operator extends Formula
{
    private $condition;
    
    public function __construct(Formula_node $left, Formula_node $right, Formula_node $condition)
    {
        parent::__construct($left, $right);
        $this->condition = $condition;
    }
    
    public function evaluate(): float
    {
        return $this->condition->evaluate() != 0 ? $this->left->evaluate() : $this->right->evaluate();
    }
    
    public function toString(): string
    {
        return "( ".$this->condition->toString() . " ? " . $this->left->toString() . " : " . $this->right->toString()." )";
    }
}