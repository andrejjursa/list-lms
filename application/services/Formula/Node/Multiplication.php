<?php

namespace Application\Services\Formula\Node;

class Multiplication extends Formula
{
    public $logic = false;
    
    public function evaluate($map): ?float
    {
        if($this->left->logic || $this->right->logic)
            return null;
    
        $left_result = $this->left->evaluate($map);
        $right_result = $this->right->evaluate($map);
        
        if($left_result === null || $right_result === null)
            return null;
        return $left_result * $right_result;
    }
    
    public function toString(): string
    {
        return "( " . $this->left->toString() . " * " . $this->right->toString() . " )";
    }
}