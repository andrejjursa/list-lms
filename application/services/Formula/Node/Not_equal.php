<?php

namespace Application\Services\Formula\Node;

class Not_equal extends Formula
{
    public $logic = true;
    
    public function evaluate($map)
    {
        return $this->left->evaluate($map) != $this->right->evaluate($map);
    }
    
    public function toString(): string
    {
        return "( " . $this->left->toString() . " != " .$this->right->toString() . " )";
    }
}