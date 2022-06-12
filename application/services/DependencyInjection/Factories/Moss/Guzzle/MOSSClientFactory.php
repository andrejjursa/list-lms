<?php

namespace Application\Services\DependencyInjection\Factories\Moss\Guzzle;

use GuzzleHttp\Client;
use Psr\Container\ContainerInterface;

class MOSSClientFactory implements \Application\Services\DependencyInjection\Factories\ServiceFactoryInterface
{

    /**
     * @inheritDoc
     */
    public function __invoke(string $id, ContainerInterface $container)
    {
        return new Client([
            'base_uri' => 'http://moss.stanford.edu',
            'timeout' => 30,
        ]);
    }
}