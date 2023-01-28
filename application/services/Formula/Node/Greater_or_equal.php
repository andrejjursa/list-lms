<?php

namespace Application\Services\Formula\Node;

class Greater_or_equal extends Formula
{
    public $logic = true;
    
    public function evaluate($map): ?float
    {
        $left_result = $this->left->evaluate($map);
        $right_result = $this->right->evaluate($map);
        
        if($left_result === null || $right_result === null)
            return null;
        if($left_result >= $right_result)
            return 1;
        return 0;
    }
    
    public function toString(): string
    {
        return "( " . $this->left->toString() . " >= " .$this->right->toString() . " )";
    }
}