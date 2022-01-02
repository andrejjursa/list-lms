<?php

use Application\Services\AMQP\Connection;
use Application\Services\AMQP\Factory\ConsumerFactory;
use Application\Services\AMQP\Factory\PublisherFactory;
use Application\Services\DependencyInjection\Factories\AmqpConnectionFactory;
use Application\Services\DependencyInjection\Factories\ConfigFactory;
use Application\Services\DependencyInjection\Factories\ConsumerFactoryObjectFactory;
use Application\Services\DependencyInjection\Factories\PublisherFactoryObjectFactory;

return [
    /* CONFIGS */
    'config_amqp'           => ConfigFactory::class,
    /* AMQP related services */
    Connection::class       => AmqpConnectionFactory::class,
    PublisherFactory::class => PublisherFactoryObjectFactory::class,
    ConsumerFactory::class  => ConsumerFactoryObjectFactory::class,
];