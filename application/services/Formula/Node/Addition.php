<?php

namespace Application\Services\Formula\Node;

class Addition extends Formula
{
    public $logic = false;
    
    public function evaluate($map): ?float
    {
        if($this->left->logic || $this->right->logic)
            return null;
        if($this->left->evaluate($map) == null || $this->right->evaluate($map) == null)
            return null;
        return $this->left->evaluate($map) + $this->right->evaluate($map);
    }
    
    public function toString(): string
    {
        return "( " . $this->left->toString() . " + " .$this->right->toString() . " )";
    }
}