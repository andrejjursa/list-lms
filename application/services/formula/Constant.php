<?php

class Constant implements Formula_node
{
    private $value;
    
    public function __construct(Float $value){
        $this->value = $value;
    }
    
    public function get_value(){
        return $this->value;
    }
    
    public function compute(): float
    {
        return $this->value;
    }
    
    public function evaluate(): bool
    {
        return $this->value == 0;
    }
    
    public function toString(): string
    {
        return $this->value.$this->toString();
    }
}