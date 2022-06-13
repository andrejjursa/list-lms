<?php

namespace Application\Services\DependencyInjection\Factories\Moss\Service;

use Application\Services\DependencyInjection\Factories\ServiceFactoryInterface;
use Application\Services\Moss\Service\ConfigurationBuilder;
use Psr\Container\ContainerInterface;

class ConfigurationBuilderFactory implements ServiceFactoryInterface
{

    /**
     * @inheritDoc
     */
    public function __invoke(string $id, ContainerInterface $container)
    {
        return new ConfigurationBuilder();
    }
}