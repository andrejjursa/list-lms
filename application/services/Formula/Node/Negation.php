<?php

namespace Application\Services\Formula\Node;

use Application\Services\Formula\Node\Formula_node;

class Negation implements Formula_node
{
    private $original_formula;
    
    public function __construct(Formula_node $original_formula){
        $this->original_formula = $original_formula;
    }
    
    public function compute(): float
    {
        // TODO: Implement compute() method.
        return 0;
    }
    
    public function evaluate(): bool
    {
        // TODO: Implement evaluate() method.
        return true;
    }
    
    public function toString(): string
    {
        return "( ¬ " . $this->original_formula->toString() . " )";
    }
}