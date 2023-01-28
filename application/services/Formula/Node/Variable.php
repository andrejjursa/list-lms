<?php

namespace Application\Services\Formula\Node;

class Variable implements Formula_node
{
    public $name;
    public $type_id;
    public $value;
    public $logic = false;
    
    public function __construct(string $name, int $type_id)
    {
        $this->name = $name;
        $this->type_id = $type_id;
    }
    
    public function evaluate($map): ?float
    {
        if (array_key_exists($this->type_id, $map)) {
            return $map[$this->type_id];
        }
        return null;
    }
    
    public function toString(): string
    {
        return '~'. $this->name;
    }
}