<?php

namespace Application\Services\DependencyInjection\Factories\Redis;

use Application\Exceptions\RedisException\UnknownRedisTypeException;
use Application\Services\DependencyInjection\Factories\ServiceFactoryInterface;
use CI_Config;
use Predis\Client;
use Psr\Container\ContainerInterface;

class RedisClientFactory implements ServiceFactoryInterface
{

    /**
     * @inheritDoc
     */
    public function __invoke(string $id, ContainerInterface $container)
    {
        $config = $container->get('config_redis');
        [$type, $postfix] = explode('_', $id, 2);
        
        return $this->loadByType($type, $config);
    }
    
    /**
     * @throws UnknownRedisTypeException
     */
    private function loadByType(string $type, CI_Config $config): Client
    {
        if ($type === 'lock') {
            /** @var array $redisConfig */
            $redisConfig = $config->item('redis');
            if (isset($redisConfig['lock']) && is_array($redisConfig['lock'])) {
                return $this->loadLockRedisClient($redisConfig['lock']);
            }
        }
        throw new UnknownRedisTypeException(
            sprintf(
                'Unknown redis type "%s".',
                $type
            )
        );
    }
    
    private function loadLockRedisClient(array $config): Client
    {
        return new Client($config);
    }
}