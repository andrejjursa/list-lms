<?php

namespace Application\Services\AMQP;

use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

trait MessageDataTrait
{
    /**
     * @param AMQPMessage $message
     *
     * @return array{contentType: ?string, data: array}
     */
    final protected function getMessageData(AMQPMessage $message): array
    {
        $output = [];
        $properties = $message->get_properties();
        $output['contentType'] = $properties['content_type'] ?? null;
        /** @var AMQPTable|null $applicationHeaders */
        $applicationHeaders = $properties['application_headers'] ?? null;
        if (null === $applicationHeaders) {
            $output['data'] = [];
        } else {
            $output['data'] = $applicationHeaders->getNativeData();
        }
        return $output;
    }
}