<?php

namespace Application\Services\Formula\Node;

class Negation implements Formula_node
{
    private $original_formula;
    
    public function __construct(Formula_node $original_formula){
        $this->original_formula = $original_formula;
    }
    
    public function evaluate(): float
    {
        $val = $this->original_formula->evaluate();
        return $val == 0;
    }
    
    public function toString(): string
    {
        return "( ¬ " . $this->original_formula->toString() . " )";
    }
}