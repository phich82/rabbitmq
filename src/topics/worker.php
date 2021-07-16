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

// Register a new exchange (direct_logs) with type as 'direct'
$exchange = 'topics_logs';
$exchangeType = 'topic';

$channel->exchange_declare($exchange, $exchangeType, false, false, false);

// Tell the exchange to send messages to our queue (binding: binding exchange to a queue)
list($queue_name,,) = $channel->queue_declare('', false, false, $durable, $auto_delete);

$binding_keys = array_slice($argv, 1);
if (empty($binding_keys)) {
    file_put_contents('php://stderr', "Usage: $argv[0] [binding_key]\n");
    exit(1);
}

foreach ($binding_keys as $binding_key) {
    $channel->queue_bind($queue_name, $exchange, $binding_key);
}

echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function ($msg) {
    echo " [x] Received ", "[", date('y-md H:i:s'), "] ", $msg->delivery_info['routing_key'], ': ', $msg->body, "\n";
};

// Listen messages from queue
$channel->basic_consume($queue_name, '', false, $noUseACK, false, false, $callback);

while ($channel->is_open()) {
    $channel->wait();
}

$channel->close();
$connection->close();

// Commands:
// - To receive all the logs
//   php worker.php "#"
// - To receive all logs from the facility "kern"
//   php worker.php "kern.*"
// - Or if you want to hear only about "critical" logs
//   php worker.php "*.critical"
// - You can create multiple bindings:
//   php worker.php "kern.*" "*.critical"
