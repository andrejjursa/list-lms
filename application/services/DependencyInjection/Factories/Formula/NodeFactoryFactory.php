<?php

namespace Application\Services\DependencyInjection\Factories\Formula;

use Application\Services\DependencyInjection\Factories\ServiceFactoryInterface;
use Application\Services\Formula\NodeFactory;
use Psr\Container\ContainerInterface;

class NodeFactoryFactory implements ServiceFactoryInterface
{
    
    /**
     * @inheritDoc
     */
    public function __invoke(string $id, ContainerInterface $container)
    {
        return new NodeFactory();
    }
}