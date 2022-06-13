<?php

namespace Application\Services\DependencyInjection\Factories;

use Application\Services\AMQP\Connection;
use CI_Config;
use Psr\Container\ContainerInterface;

class AmqpConnectionFactory implements ServiceFactoryInterface
{
    
    /**
     * @inheritDoc
     */
    public function __invoke(string $id, ContainerInterface $container)
    {
        /** @var CI_Config $config */
        $configAmqp = $container->get('config_amqp');
        
        return new Connection(
            $configAmqp->item('amqp_host'),
            $configAmqp->item('amqp_port'),
            $configAmqp->item('amqp_user'),
            $configAmqp->item('amqp_password'),
            $configAmqp->item('amqp_vhost')
        );
    }
}