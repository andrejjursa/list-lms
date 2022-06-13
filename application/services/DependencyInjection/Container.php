<?php

namespace Application\Services\DependencyInjection;

use Application\Services\DependencyInjection\Exception\ContainerException;
use Application\Services\DependencyInjection\Exception\NotFoundException;
use Application\Services\DependencyInjection\Factories\ServiceFactoryInterface;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    /**
     * @var array<string, callable>
     */
    protected $factories = [];
    
    /**
     * @param string       $id
     * @param class-string $factory
     *
     * @return void
     */
    public function addServiceFactory(string $id, string $factory): void
    {
        $this->factories[$id] = static function (string $id, ContainerInterface $container) use ($factory) {
            $factoryObject = new $factory();
            if ($factoryObject instanceof ServiceFactoryInterface) {
                return $factoryObject($id, $container);
            }
            throw new ContainerException(
                sprintf(
                    'Factory "%s" for service "%s" is not instance of "%s".',
                    $factory,
                    $id,
                    ServiceFactoryInterface::class
                )
            );
        };
    }
    
    /**
     * @inheritDoc
     */
    public function get($id)
    {
        if (!$this->has($id)) {
            throw new NotFoundException($id);
        }
        
        $factory = $this->factories[$id];
        
        try {
            return $factory($id, $this);
        } catch (\Exception|\Throwable $exception) {
            throw new ContainerException(
                sprintf('Failed to construct service "%s".', $id),
                0,
                $exception
            );
        }
    }
    
    /**
     * @inheritDoc
     */
    public function has($id): bool
    {
        return array_key_exists($id, $this->factories);
    }
}