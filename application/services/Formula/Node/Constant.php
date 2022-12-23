<?php

namespace Application\Services\Formula\Node;

class Constant implements Formula_node
{
    public $value;
    public $logic = false;
    
    public function __construct(float $value){
        $this->value = $value;
    }
    
    public function evaluate($map): float
    {
        return $this->value;
    }
    
    public function toString(): string
    {
        return (string) $this->value;
    }
}