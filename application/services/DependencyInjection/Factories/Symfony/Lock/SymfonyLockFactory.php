<?php

namespace Application\Services\DependencyInjection\Factories\Symfony\Lock;

use Application\Services\DependencyInjection\Factories\ServiceFactoryInterface;
use Predis\Client;
use Psr\Container\ContainerInterface;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\RedisStore;

class SymfonyLockFactory implements ServiceFactoryInterface
{

    /**
     * @inheritDoc
     */
    public function __invoke(string $id, ContainerInterface $container)
    {
        /** @var Client $lockRedis */
        $lockRedis = $container->get('lock_redis');
        $store = new RedisStore($lockRedis);
        return new LockFactory($store);
    }
}