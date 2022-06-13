<?php

namespace Application\Services\DependencyInjection\Factories;

use CI_Controller;
use Psr\Container\ContainerInterface;
use RuntimeException;

class ConfigFactory implements ServiceFactoryInterface
{
    
    /**
     * @inheritDoc
     */
    public function __invoke(string $id, ContainerInterface $container)
    {
        [$prefix, $config] = explode('_', $id, 2);
        /** @var CI_Controller $CI */
        $CI =& get_instance();
        
        if (!$CI->config->load($config)) {
            throw new RuntimeException(sprintf('Failed to load config "%s".', $config));
        }
        
        return $CI->config;
    }
}