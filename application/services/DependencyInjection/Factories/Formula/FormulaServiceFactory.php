<?php

namespace Application\Services\DependencyInjection\Factories\Formula;

use Application\Services\DependencyInjection\Factories\ServiceFactoryInterface;
use Application\Services\Formula\FormulaService;
use Application\Services\Formula\NodeFactory;
use Psr\Container\ContainerInterface;

class FormulaServiceFactory implements ServiceFactoryInterface
{
    public function __invoke(string $id, ContainerInterface $container)
    {
        return new FormulaService($container->get(NodeFactory::class));
    }
}