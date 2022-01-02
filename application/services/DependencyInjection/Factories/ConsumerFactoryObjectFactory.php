<?php

namespace Application\Services\DependencyInjection\Factories;

use Application\Services\AMQP\Connection;
use Application\Services\AMQP\Factory\ConsumerFactory;
use Psr\Container\ContainerInterface;

class ConsumerFactoryObjectFactory implements ServiceFactoryInterface
{
    
    /**
     * @inheritDoc
     */
    public function __invoke(string $id, ContainerInterface $container)
    {
        return new ConsumerFactory(
            $container->get(Connection::class)
        );
    }
}