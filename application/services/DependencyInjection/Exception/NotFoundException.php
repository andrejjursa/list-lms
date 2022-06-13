<?php

namespace Application\Services\DependencyInjection\Exception;

use Exception;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

class NotFoundException extends Exception implements NotFoundExceptionInterface
{
    /**
     * @inheritDoc
     */
    public function __construct($service, $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('Service "%s" not found in container.', $service), $code, $previous);
    }
}