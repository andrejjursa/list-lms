<?php

namespace Application\Services\Formula\Node;

class MaxFunction extends Formula
{
    public $logic = false;
    
    public function evaluate($map): ?float
    {
        $left_result = $this->left->evaluate($map);
        $right_result = $this->right->evaluate($map);
        
        if($left_result == null || $right_result == null)
            return null;
        
        return $left_result > $right_result ? $left_result : $right_result;
    }
    
    public function toString(): string
    {
        return "%MAX( " . $this->left->toString() . " , " .$this->right->toString() . " )";
    }
}