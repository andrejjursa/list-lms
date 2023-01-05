<?php

namespace Application\Services\Formula\Node;

class Disjunction extends Formula
{
    public $logic = true;
    
    public function evaluate($map): ?float
    {
        if($this->left->evaluate($map) == null || $this->right->evaluate($map) == null)
            return null;
        if($this->left->evaluate($map) || $this->right->evaluate($map))
            return 1;
        return 0;
    }
    
    public function toString(): string
    {
        return "( " . $this->left->toString() . " v " .$this->right->toString() . " )";
    }
}