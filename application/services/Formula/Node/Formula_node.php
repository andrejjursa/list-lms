<?php

namespace Application\Services\Formula\Node;

interface Formula_node
{
    public function evaluate($map): ?float;
    public function toString(): string;
}