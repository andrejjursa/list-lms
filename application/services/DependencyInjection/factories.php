<?php

use Application\Services\AMQP\Connection;
use Application\Services\AMQP\Factory\ConsumerFactory;
use Application\Services\AMQP\Factory\PublisherFactory;
use Application\Services\DependencyInjection\Factories\AmqpConnectionFactory;
use Application\Services\DependencyInjection\Factories\ConfigFactory;
use Application\Services\DependencyInjection\Factories\ConsumerFactoryObjectFactory;
use Application\Services\DependencyInjection\Factories\GetComparisonsRequestMapperFactory;
use Application\Services\DependencyInjection\Factories\Moss\Service\ConfigurationBuilderFactory;
use Application\Services\DependencyInjection\Factories\Moss\Service\MossExecutionServiceFactory;
use Application\Services\DependencyInjection\Factories\MossRequestFactoryFactory;
use Application\Services\DependencyInjection\Factories\PublisherFactoryObjectFactory;
use Application\Services\DependencyInjection\Factories\Redis\RedisClientFactory;
use Application\Services\DependencyInjection\Factories\Symfony\Lock\SymfonyLockFactory;
use Application\Services\Moss\RequestFactory as MossRequestFactory;
use Application\Services\Moss\RequestMapper\GetComparisonsRequestMapper;
use Application\Services\Moss\Service\ConfigurationBuilder;
use Application\Services\Moss\Service\MossExecutionService;

return [
    /* CONFIGS */
    'config_amqp'                      => ConfigFactory::class,
    'config_redis'                     => ConfigFactory::class,
    /* AMQP related services */
    Connection::class                  => AmqpConnectionFactory::class,
    PublisherFactory::class            => PublisherFactoryObjectFactory::class,
    ConsumerFactory::class             => ConsumerFactoryObjectFactory::class,
    /* MOSS related services */
    GetComparisonsRequestMapper::class => GetComparisonsRequestMapperFactory::class,
    MossRequestFactory::class          => MossRequestFactoryFactory::class,
    ConfigurationBuilder::class        => ConfigurationBuilderFactory::class,
    MossExecutionService::class        => MossExecutionServiceFactory::class,
    /* Redis */
    'lock_redis'                       => RedisClientFactory::class,
    /* Symfony/Lock */
    'symfony_lock_factory'             => SymfonyLockFactory::class,
];