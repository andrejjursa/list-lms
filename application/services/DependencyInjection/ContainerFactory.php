<?php

namespace Application\Services\DependencyInjection;

use Psr\Container\ContainerInterface;

class ContainerFactory
{
    /** @var ContainerInterface */
    protected static $container;
    
    protected static function construct(): void
    {
        $factories = include(__DIR__ . DIRECTORY_SEPARATOR . 'factories.php');
        self::$container = new Container();
        foreach ($factories as $service => $factory) {
            self::$container->addServiceFactory($service, $factory);
        }
    }
    
    public static function getContainer(): ContainerInterface
    {
        if (!(self::$container instanceof ContainerInterface)) {
            self::construct();
        }
        return self::$container;
    }
}