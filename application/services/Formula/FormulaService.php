<?php

namespace Application\Services\Formula;

use Application\Services\Formula\Node\Constant; // TODO REMOVE
use Application\Services\Formula\Node\Formula_node;

class FormulaService
{
    /** @var NodeFactory */
    private $nodeFactory;
    
    public function __construct(NodeFactory $nodeFactory)
    {
        $this->nodeFactory = $nodeFactory;
    }
    
    public function build($input, $types): ?Formula_node
    {
        return $this->nodeFactory->getAddition(new Constant(5), new Constant(10));
        // TODO parse input formula string
    }
    
}
