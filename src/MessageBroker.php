<?php

namespace App;

use Enqueue\AmqpLib\AmqpConnectionFactory;
use Interop\Amqp\AmqpContext;

class MessageBroker
{
    private AmqpConnectionFactory $connection;

    /**
     * MessageBroker constructor.
     *
     * @see https://php-enqueue.github.io/transport/amqp_lib/#create-context
     */
    private function __construct()
    {
        $this->connection = new AmqpConnectionFactory([
            'host' => 'ac_rabbitmq',
            'port' => 5672,
            'user' => 'guest',
            'pass' => 'guest',
            'vhost' => '/',
            'persisted' => true,
        ]);
    }

    /**
     * @return AmqpContext
     */
    public static function context(): AmqpContext
    {
        $me = new self();
        return $me->connection->createContext();
    }
}