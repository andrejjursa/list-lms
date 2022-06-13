<?php

namespace Application\Services\DependencyInjection\Factories;

use Application\Services\Moss\RequestMapper\GetComparisonsRequestMapper;
use Psr\Container\ContainerInterface;

class GetComparisonsRequestMapperFactory implements ServiceFactoryInterface
{
    
    /**
     * @inheritDoc
     */
    public function __invoke(string $id, ContainerInterface $container)
    {
        return new GetComparisonsRequestMapper();
    }
}