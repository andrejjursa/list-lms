<?php

namespace Application\Services\AMQP;

use ErrorException;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use PhpAmqpLib\Message\AMQPMessage;
use Rector\PHPOffice\ValueObject\PHPExcelMethodDefaultValues;

abstract class AbstractConsumer implements ConsumerInterface
{
    use ChannelTrait;
    
    protected const CONSUMER_TAG = 'consumer';
    
    /** @var bool */
    private $running = false;
    
    /** @var bool */
    private $restart = true;
    
    /** @var MessageReconstruction */
    private $messageReconstruction;
    
    /**
     * @param Connection        $connection
     * @param QueueInterface    $queue
     * @param ExchangeInterface $exchange
     */
    public function __construct(Connection $connection, QueueInterface $queue, ExchangeInterface $exchange)
    {
        $this->connection = $connection;
        $this->queue = $queue;
        $this->exchange = $exchange;
        $this->messageReconstruction = new MessageReconstruction();
    }
    
    protected function getMessageReconstruction(): MessageReconstruction
    {
        return $this->messageReconstruction;
    }
    
    final public function consumeQueue(): void
    {
        $this->attachSignals();
        
        $this->running = false;
        $this->restart = true;
        
        while ($this->running || $this->restart) {
            $this->restart = false;
            try {
                $this->doConsume();
            } catch (\Throwable $exception) {
                echo 'Message: ' . $exception->getMessage() . PHP_EOL;
                echo 'File: ' . $exception->getFile() . PHP_EOL;
                echo 'Line: ' . $exception->getLine() . PHP_EOL;
                echo 'Code: ' . $exception->getCode() . PHP_EOL;
                echo $exception->getTraceAsString() . PHP_EOL;
            }
        }
    }
    
    private function doConsume(): void
    {
        $channel = $this->getChannel();
        
        $callback = function (AMQPMessage $message) {
            $this->processMessage($message);
        };
        
        echo 'Starting consumer: ' . static::CONSUMER_TAG . PHP_EOL;
        
        $this->running = true;
        $channel->basic_consume(
            $this->queue->getName(),
            static::CONSUMER_TAG,
            false,
            false,
            false,
            false,
            $callback
        );
        
        while ($channel->is_consuming() && $this->running) {
            try {
                $channel->wait(null, false, 5);
                pcntl_signal_dispatch();
            } catch (ErrorException|AMQPTimeoutException $e) {
            }
            if (!$this->running) {
                echo 'Terminating consumer: ' . static::CONSUMER_TAG . PHP_EOL;
            }
        }
    }
    
    private function attachSignals(): void
    {
        $handler = function ($signal) {
            if ($signal === SIGHUP) {
                $this->restart = true;
            }
            $this->running = false;
        };
        
        pcntl_signal(SIGTERM, $handler);
        pcntl_signal(SIGINT, $handler);
        pcntl_signal(SIGHUP, $handler);
        pcntl_signal(SIGUSR1, $handler);
    }
    
}