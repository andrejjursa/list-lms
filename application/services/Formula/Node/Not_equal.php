<?php

namespace Application\Services\Formula\Node;

class Not_equal extends Formula
{
    public function evaluate(): float
    {
        // TODO: Implement evaluate() method.
        return 0;
    }
    
    public function toString(): string
    {
        return "( " . $this->left->toString() . " != " .$this->right->toString() . " )";
    }
}