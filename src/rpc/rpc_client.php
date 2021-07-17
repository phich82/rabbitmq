<?php

/**
 * AS PUBLISHER (PRODUCER/SENDER)
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class FibonacciRpcClient {
    private $connection;
    private $channel;
    private $callback_queue;
    private $response;
    private $corr_id;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $this->channel = $this->connection->channel();

        list($this->callback_queue, ,) = $this->channel->queue_declare("", false, false, true, false);

        // Listening result from server
        $this->channel->basic_consume($this->callback_queue, '', false, true, false, false, [$this, 'onResponse']);
    }

    /**
     * Handle response from server
     *
     * @param  object $rep
     * @return void
     */
    public function onResponse($rep)
    {
        if ($rep->get('correlation_id') == $this->corr_id) {
            $this->response = $rep->body;
        }
    }

    /**
     * Request for calulating fibonacci from server
     *
     * @param  mixed $n
     * @return void
     */
    public function call($n)
    {
        $this->response = null;
        // Create unique correlation id for every request on queue
        $this->corr_id = uniqid();
        // Create message for pushing on queue
        $msg = new AMQPMessage(
            (string) $n,
            [
                'correlation_id' => $this->corr_id,
                'reply_to' => $this->callback_queue
            ]
        );
        // Push on queue (rpc)
        $this->channel->basic_publish($msg, '', 'rpc_queue');
        // Waiting response from server
        while (!$this->response) {
            $this->channel->wait();
        }
        // Return result calculated from server
        return intval($this->response);
    }
}

$fibonacci_rpc = new FibonacciRpcClient();
$response = $fibonacci_rpc->call(30);
echo ' [.] Got ', $response, "\n";
