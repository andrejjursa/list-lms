<?php

namespace Application\Services\Formula\Node;

class Ternary_operator extends Formula
{
    public $condition;
    public $logic = false;
    
    public function __construct(Formula_node $left, Formula_node $right, Formula_node $condition)
    {
        parent::__construct($left, $right);
        $this->condition = $condition;
    }
    
    public function evaluate($map): ?float
    {
        if(! $this->condition->logic)
            return null;
        if($this->left->evaluate($map) == null || $this->right->evaluate($map) == null)
            return null;
        return $this->condition->evaluate($map) != 0 ? $this->left->evaluate($map) : $this->right->evaluate($map);
    }
    
    public function toString(): string
    {
        return "( ".$this->condition->toString() . " ? " . $this->left->toString() . " : " . $this->right->toString()." )";
    }
}