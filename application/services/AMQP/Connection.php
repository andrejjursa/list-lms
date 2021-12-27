<?php

namespace Application\Services\AMQP;

use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class Connection
{
    /** @var string */
    protected $host;
    
    /** @var int */
    protected $port;
    
    /** @var string */
    protected $user;
    
    /** @var string */
    protected $password;
    
    /** @var string */
    protected $vhost;
    
    /** @var AbstractConnection|null */
    protected $connection;
    
    /**
     * @param string $host
     * @param int    $port
     * @param string $user
     * @param string $password
     * @param string $vhost
     */
    public function __construct(string $host, int $port, string $user, string $password, string $vhost)
    {
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->password = $password;
        $this->vhost = $vhost;
    }
    
    public function getConnection(): AbstractConnection
    {
        if (null === $this->connection) {
            $this->connection = new AMQPStreamConnection(
                $this->host,
                $this->port,
                $this->user,
                $this->password,
                $this->vhost
            );
        }
        
        return $this->connection;
    }
}