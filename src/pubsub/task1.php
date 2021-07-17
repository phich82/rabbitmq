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
$exchange = 'logs';
$exchangeType = 'fanout';

$channel->exchange_declare($exchange, $exchangeType, false, false, false);

// Prepare message for sending
$data = implode(' ', array_slice($argv, 1));
if (empty($data)) {
    $data = 'info: Hello World!';
}

$msg = new AMQPMessage($data);

// Publish message to exchange
$channel->basic_publish($msg, $exchange);

$datetime = date('Y-m-d H:i:s');
echo " [x] Sent [{$datetime}] '{$data}'\n";

$channel->close();
$connection->close();
