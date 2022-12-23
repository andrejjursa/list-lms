<?php

namespace Application\Services\Formula\Node;

interface Formula_node
{
    public function evaluate($map);
    public function toString(): string;
}