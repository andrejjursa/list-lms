<?php

namespace Application\Services\Formula\Node;

interface Formula_node
{
    public function compute(): float;  // vrati null ak sa neda vyhodnotit
    public function evaluate(): bool;
    public function toString(): string;
}