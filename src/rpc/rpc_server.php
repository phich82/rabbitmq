<?php

/**
 * AS SUBSCRIBER (CONSUMER/RECEIVER)
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

$queue = 'rpc_queue';

$channel->queue_declare($queue, false, false, false, false);

// Calculate fibonacci
function fib($n) {
    if ($n == 0) {
        return 0;
    }
    if ($n == 1) {
        return 1;
    }
    return fib($n-1) + fib($n-2);
}

echo " [x] Awaiting RPC requests\n";

$callback = function ($req) {
    // Calculate fibonacci based on input from client
    $n = intval($req->body);
    echo ' [.] fib(', $n, ")\n";
    // Create message for sending result back client
    $msg = new AMQPMessage(
        (string) fib($n),
        ['correlation_id' => $req->get('correlation_id')]
    );
    // Send result calculated back to client
    $req->delivery_info['channel']->basic_publish(
        $msg,
        '',
        $req->get('reply_to')
    );
    // Send back queue if error
    $req->ack();
};

// Listen messages from queue
$channel->basic_qos(null, 1, null);
$channel->basic_consume($queue, '', false, false, false, false, $callback);

while ($channel->is_open()) {
    $channel->wait();
}

$channel->close();
$connection->close();
