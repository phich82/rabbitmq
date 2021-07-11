<?php

/**
 * AS PUBLISHER (PRODUCER/SENDER)
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

// Declare exchange with its type
$exchange = 'topics_logs';
$exchangeType = 'topic';

$channel->exchange_declare($exchange, $exchangeType, false, false, false);

// Prepare message for sending
$routing_key= isset($argv[1]) && !empty($argv[1]) ? $argv[1] : 'anonymous.info';
$data = implode(' ', array_slice($argv, 2));
if (empty($data)) {
    $data = "Hello World!";
}

$msg = new AMQPMessage($data);

// Publish message to exchange via routing_key
$channel->basic_publish($msg, $exchange, $routing_key);

$datetime = date('Y-m-d H:i:s');
echo " [x] Sent [{$datetime}] {$routing_key}: '{$data}'\n";

$channel->close();
$connection->close();
