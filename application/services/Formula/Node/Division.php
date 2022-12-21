<?php

namespace Application\Services\Formula\Node;

class Division extends Formula
{
    public function evaluate(): float
    {
        // TODO: Implement evaluate() method.
        return 0;
    }
    
    public function toString(): string
    {
        return "( " . $this->left->toString() . " / " .$this->right->toString() . " )";
    }
}