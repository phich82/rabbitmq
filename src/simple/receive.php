<?php

/**
 * AS SUBSCRIBER (CONSUMER/RECEIVER)
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

// Declare queue name for receiving
$queue = 'hello';
$channel->queue_declare($queue, false, false, false, false);

echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function ($msg) {
    echo ' [x] Received ', '[', date('Y-m-d H:i:s'), '] ', $msg->body, "\n";
};

// Listen messages from queue
$channel->basic_consume($queue, '', false, true, false, false, $callback);

while ($channel->is_open()) {
    $channel->wait();
}

$channel->close();
$connection->close();
