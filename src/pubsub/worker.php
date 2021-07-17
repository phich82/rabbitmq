<?php

/**
 * AS SUBSCRIBER (CONSUMER/RECEIVER)
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

// Use ack: resend to queue, but messages can be lost if RabbitMQ Server died
$noUseACK = true;
$durable = true;
$auto_delete = false;

// Register a new exchange (logs) with type as 'fanout'
$exchange = 'logs';
$exchangeType = 'fanout';

$channel->exchange_declare($exchange, $exchangeType, false, false, false);

// Tell the exchange to send messages to our queue (binding: binding exchange to a queue)
list($queue_name,,) = $channel->queue_declare('', false, false, $durable, $auto_delete);
$channel->queue_bind($queue_name, $exchange);

echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function ($msg) {
    echo " [x] Received ", "[", date('y-md H:i:s'), "] ", $msg->body, "\n";
};

// Listen messages from queue
$channel->basic_consume($queue_name, '', false, $noUseACK, false, false, $callback);

while ($channel->is_open()) {
    $channel->wait();
}

$channel->close();
$connection->close();
