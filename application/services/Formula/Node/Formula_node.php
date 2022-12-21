<?php

namespace Application\Services\Formula\Node;

interface Formula_node
{
    public function evaluate(): float; // vrati null ak sa neda vyhodnotit
    public function toString(): string;
}