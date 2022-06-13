<?php

namespace Application\Services\DependencyInjection\Factories;

use Application\Services\AMQP\Connection;
use Application\Services\AMQP\Factory\ConsumerFactory;
use Application\Services\AMQP\Factory\PublisherFactory;
use Application\Services\Moss\Service\MossExecutionService;
use Psr\Container\ContainerInterface;

class ConsumerFactoryObjectFactory implements ServiceFactoryInterface
{
    
    /**
     * @inheritDoc
     */
    public function __invoke(string $id, ContainerInterface $container)
    {
        return new ConsumerFactory(
            $container->get(Connection::class),
            $container->get(MossExecutionService::class),
            $container->get(PublisherFactory::class),
            $container->get('symfony_lock_factory')
        );
    }
}