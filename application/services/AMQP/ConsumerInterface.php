<?php

namespace Application\Services\AMQP;

use PhpAmqpLib\Message\AMQPMessage;

interface ConsumerInterface
{
    public function processMessage(AMQPMessage $message): void;
}