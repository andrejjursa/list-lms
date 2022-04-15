<?php

use Application\Services\AMQP\Connection;
use Application\Services\AMQP\Factory\ConsumerFactory;
use Application\Services\AMQP\Factory\PublisherFactory;
use Application\Services\DependencyInjection\Factories\AmqpConnectionFactory;
use Application\Services\DependencyInjection\Factories\ConfigFactory;
use Application\Services\DependencyInjection\Factories\ConsumerFactoryObjectFactory;
use Application\Services\DependencyInjection\Factories\GetComparisonsRequestMapperFactory;
use Application\Services\DependencyInjection\Factories\MossRequestFactoryFactory;
use Application\Services\DependencyInjection\Factories\PublisherFactoryObjectFactory;
use Application\Services\Moss\RequestFactory as MossRequestFactory;
use Application\Services\Moss\RequestMapper\GetComparisonsRequestMapper;

return [
    /* CONFIGS */
    'config_amqp'           => ConfigFactory::class,
    /* AMQP related services */
    Connection::class       => AmqpConnectionFactory::class,
    PublisherFactory::class => PublisherFactoryObjectFactory::class,
    ConsumerFactory::class  => ConsumerFactoryObjectFactory::class,
    GetComparisonsRequestMapper::class => GetComparisonsRequestMapperFactory::class,
    MossRequestFactory::class => MossRequestFactoryFactory::class,
];