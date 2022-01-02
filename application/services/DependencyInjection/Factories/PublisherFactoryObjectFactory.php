<?php

namespace Application\Services\DependencyInjection\Factories;

use Application\Services\AMQP\Connection;
use Application\Services\AMQP\Factory\PublisherFactory;
use Psr\Container\ContainerInterface;

class PublisherFactoryObjectFactory implements ServiceFactoryInterface
{
    
    /**
     * @inheritDoc
     */
    public function __invoke(string $id, ContainerInterface $container)
    {
        return new PublisherFactory(
            $container->get(Connection::class)
        );
    }
}