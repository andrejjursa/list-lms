<?php

namespace Application\Services\AMQP\Factory;

use Application\Services\AMQP\Connection;
use Application\Services\AMQP\Consumers\Moss\MossConsumer;
use Application\Services\AMQP\Consumers\TestConsumer;
use Application\Services\AMQP\Exchanges\DirectExchange;
use Application\Services\AMQP\Exchanges\Moss\MossExchange;
use Application\Services\AMQP\Queues\Moss\ComparisonQueue;
use Application\Services\AMQP\Queues\TestQueue;
use Application\Services\Moss\Service\MossExecutionService;

class ConsumerFactory
{
    /** @var Connection */
    protected $connection;
    
    /** @var DirectExchange */
    protected $directExchange;
    
    /** @var MossExchange */
    protected $mossExchange;
    
    /** @var MossExecutionService */
    protected $mossExecutionService;
    
    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection, MossExecutionService $mossExecutionService)
    {
        $this->connection = $connection;
        $this->directExchange = new DirectExchange();
        $this->mossExchange = new MossExchange();
        $this->mossExecutionService = $mossExecutionService;
    }
    
    public function getTestConsumer(): TestConsumer
    {
        return new TestConsumer($this->connection, new TestQueue(), $this->directExchange);
    }
    
    public function getMossConsumer(): MossConsumer
    {
        return new MossConsumer(
            $this->connection,
            new ComparisonQueue(),
            $this->mossExchange,
            $this->mossExecutionService
        );
    }
}