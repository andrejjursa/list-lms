<?php

class Variable implements Formula_node
{
    private $name;
    private $type_id;
    private $value;
    
    public function __construct(string $name, int $type_id)
    {
        $this->name = $name;
        $this->type_id = $type_id;
    }
    
    public function get_name(): string
    {
        return $this->name;
    }
    
    public function get_type_id(): int
    {
        return $this->type_id;
    }
    
    public function get_value(): float
    {
        return $this->value;
    }
    
    public function set_value(float $value)
    {
        $this->value = $value;
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
        return $this->name;
    }
}