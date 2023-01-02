<?php

namespace Application\Services\Formula\Node;

class Negation implements Formula_node
{
    public $original_formula;
    public $logic = true;
    
    public function __construct(Formula_node $original_formula){
        $this->original_formula = $original_formula;
    }
    
    public function evaluate($map)
    {
        $val = $this->original_formula->evaluate($map);
        if($val == null)
            return null;
        return $val == 0;
    }
    
    public function toString(): string
    {
        return "( ¬ " . $this->original_formula->toString() . " )";
    }
}