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
    
    public function evaluate($map)
    {
        if (array_key_exists($this->type_id,$map)) {
            $value = $map[$this->type_id];
            return $value;
        }
        return 0;
    }
    
    public function toString(): string
    {
        return $this->name;
    }
}