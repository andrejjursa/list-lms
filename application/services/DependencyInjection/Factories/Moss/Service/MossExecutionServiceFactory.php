<?php

namespace Application\Services\DependencyInjection\Factories\Moss\Service;

use Application\Services\DependencyInjection\Factories\ServiceFactoryInterface;
use Application\Services\Moss\Service\ConfigurationBuilder;
use Application\Services\Moss\Service\MossExecutionService;
use Psr\Container\ContainerInterface;

class MossExecutionServiceFactory implements ServiceFactoryInterface
{

    /**
     * @inheritDoc
     */
    public function __invoke(string $id, ContainerInterface $container)
    {
        return new MossExecutionService($container->get(ConfigurationBuilder::class));
    }
}