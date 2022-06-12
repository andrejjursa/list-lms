<?php

namespace Application\Services\DependencyInjection\Factories;

use Psr\Container\ContainerInterface;

interface ServiceFactoryInterface
{
    /**
     * @param string             $id
     * @param ContainerInterface $container
     *
     * @return mixed
     */
    public function __invoke(string $id, ContainerInterface $container);
}