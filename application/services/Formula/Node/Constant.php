<?php

namespace Application\Services\Formula\Node;

class Constant implements Formula_node
{
    public $value;
    
    public function __construct(float $value){
        $this->value = $value;
    }
    
    public function evaluate(): float
    {
        return $this->value != null;
    }
    
    public function toString(): string
    {
        return (string) $this->value;
    }
}