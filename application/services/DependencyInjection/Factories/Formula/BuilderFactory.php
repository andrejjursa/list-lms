<?php

namespace Application\Services\DependencyInjection\Factories\Formula;

use Application\Services\DependencyInjection\Factories\ServiceFactoryInterface;
use Application\Services\Formula\Builder;
use Application\Services\Formula\NodeFactory;
use Psr\Container\ContainerInterface;

class BuilderFactory implements ServiceFactoryInterface
{
    public function __invoke(string $id, ContainerInterface $container)
    {
        return new Builder($container->get(NodeFactory::class));
    }
}