<?php

namespace Application\Services\DependencyInjection\Factories;

use Application\Services\Moss\RequestFactory;
use Application\Services\Moss\RequestMapper\GetComparisonsRequestMapper;
use Psr\Container\ContainerInterface;

class MossRequestFactoryFactory implements ServiceFactoryInterface
{

    /**
     * @inheritDoc
     */
    public function __invoke(string $id, ContainerInterface $container)
    {
        return new RequestFactory($container->get(GetComparisonsRequestMapper::class));
    }
}