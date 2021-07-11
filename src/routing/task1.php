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
$exchange = 'direct_logs';
$exchangeType = 'direct';

$channel->exchange_declare($exchange, $exchangeType, false, false, false);

// Prepare message for sending
$severity = isset($argv[1]) && !empty($argv[1]) ? $argv[1] : 'info';
$data = implode(' ', array_slice($argv, 2));
if (empty($data)) {
    $data = "Hello World!";
}

$msg = new AMQPMessage($data);

// Publish message to exchange via routing_key
$channel->basic_publish($msg, $exchange, $severity);

$datetime = date('Y-m-d H:i:s');
echo " [x] Sent [{$datetime}] {$severity}: '{$data}'\n";

$channel->close();
$connection->close();
