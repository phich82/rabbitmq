<?php

/**
 * AS PUBLISHER (PRODUCER/SENDER)
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

// Use durability: Sure messages will not lost even if RabbitMQ Service died
// Notes: RabbitMQ doesn't allow you to redefine an existing queue with different parameters
$durable = true; // make sure that queue will survive when RabbitMQ Server restart

// Declare queue name for sending
$queue = 'task_durable_queue';
$channel->queue_declare($queue, false, $durable, false, false);

// Prepare message for sending
$data = implode(' ', array_slice($argv, 1));
if (empty($data)) {
    $data = 'Hello World!';
}
// Mark our messages as persistent - by setting the delivery_mode = 2 message property
// which AMQPMessage takes as part of the property array
$msg = new AMQPMessage($data, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);

// Publish message to queue
$channel->basic_publish($msg, '', $queue);

$datetime = date('Y-m-d H:i:s');;
echo " [x] Sent [{$datetime}] '{$data}'\n";

$channel->close();
$connection->close();
