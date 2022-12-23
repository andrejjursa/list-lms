<?php

namespace Application\Services\Formula\Node;

class Multiplication extends Formula
{
    public $logic = false;
    
    public function evaluate($map)
    {
        if($this->left->logic || $this->right->logic)
            return null;
        return $this->left->evaluate($map) * $this->right->evaluate($map);
    }
    
    public function toString(): string
    {
        return "( " . $this->left->toString() . " * " . $this->right->toString() . " )";
    }
}