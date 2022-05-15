<?php

namespace Application\Services\DependencyInjection\Factories\Moss\Service;

use Application\Services\DependencyInjection\Factories\ServiceFactoryInterface;
use Application\Services\Moss\Service\MossCleanUpService;
use Psr\Container\ContainerInterface;

class MossCleanUpServiceFactory implements ServiceFactoryInterface
{

    /**
     * @inheritDoc
     */
    public function __invoke(string $id, ContainerInterface $container)
    {
        return new MossCleanUpService($container->get('moss_http_client'));
    }
}