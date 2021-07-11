<?php

/**
 * AS PUBLISHER (PRODUCER/SENDER)
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

// Declare queue name for sending
$queue = 'hello';
$channel->queue_declare($queue, false, false, false, false);

$data = 'Hello World!';
$msg = new AMQPMessage($data);

// Publish message to queue
$channel->basic_publish($msg, '', $queue);

$datetime = date('Y-m-d H:i:s');
echo " [x] Sent [{$datetime}] '{$data}'\n";

$channel->close();
$connection->close();
