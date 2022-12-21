<?php

namespace Application\Services\Formula;

use Application\Services\Formula\Node\Constant;
use Application\Services\Formula\Node\Formula_node;

class Builder
{
    /** @var NodeFactory */
    private $nodeFactory;
    
    public function __construct(NodeFactory $nodeFactory)
    {
        $this->nodeFactory = $nodeFactory;
    }
    
    public function build($input): ?Formula_node
    {
        return $this->nodeFactory->getAddition(new Constant(5), new Constant(10));
    }
}