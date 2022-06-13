<?php

namespace Application\Services\AMQP;

use PhpAmqpLib\Message\AMQPMessage;

class MessageReconstruction
{
    use MessageDataTrait;
    
    public function reconstructMessage(AMQPMessage $message): ?MessageInterface
    {
        $messageData = $this->getMessageData($message);
        
        if ($messageData['contentType'] !== 'application/json' || !isset($messageData['data']['x_message_class'])) {
            return null;
        }
        
        if (!class_exists($messageData['data']['x_message_class'])) {
            return null;
        }
        
        $jsonData = json_decode($message->getBody(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }
        
        $messageObject = new $messageData['data']['x_message_class']();
        
        try {
            $reflection = new \ReflectionClass($messageObject);
        } catch (\ReflectionException $e) {
            return null;
        }
        
        foreach ($jsonData as $property => $data) {
            if ($reflection->hasProperty($property)) {
                $propertyReflection = $reflection->getProperty($property);
                if (!$propertyReflection->isPublic()) {
                    $propertyReflection->setAccessible(true);
                }
                $propertyReflection->setValue($messageObject, $data);
            }
        }
        
        return $messageObject;
    }
}