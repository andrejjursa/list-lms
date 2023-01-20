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
    
        $left_result = $this->left->evaluate($map);
        $right_result = $this->right->evaluate($map);
        
        if($left_result == null || $right_result == null)
            return null;
        return $this->condition->evaluate($map) != 0 ? $left_result : $right_result;
    }
    
    public function toString(): string
    {
        return "( ".$this->condition->toString() . " ? " . $this->left->toString() . " : " . $this->right->toString()." )";
    }
}