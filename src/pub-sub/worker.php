<?php

/**
 * AS SUBSCRIBER (CONSUMER/RECEIVER)
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

// Use ack: resend to queue, but messages can be lost if RabbitMQ Server died
$noUseACK = false;
// Use durability: Sure all messages will not lost even if RabbitMQ Service died
// Notes: RabbitMQ doesn't allow you to redefine an existing queue with different parameters
$durable = true; // make sure that queue will survive when RabbitMQ Server restart

// Declare queue name for receiving
$queue = 'task_durable_queue';
$channel->queue_declare($queue, false, $durable, false, false);

echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function ($msg) {
    // echo " [x] Received {$msg->body}, \n";
    echo " [x] Received ", "[", date('y-md H:i:s'), "] ", $msg->body, "\n";
    sleep(substr_count($msg->body, '.'));
    echo " [x] Done\n";
    // Resend message to queue
    $msg->ack();
};

// This tells RabbitMQ not to give more than one message to a worker at a time.
// Or, in other words, don't dispatch a new message to a worker until it has
// processed and acknowledged the previous one. Instead, it will dispatch it to
// the next worker that is not still busy.
$channel->basic_qos(null, 1, null);

// Listen messages from queue
$channel->basic_consume($queue, '', false, $noUseACK, false, false, $callback);

while ($channel->is_open()) {
    $channel->wait();
}

/** 1. Round-robin dispatching */
//     By default, RabbitMQ will send each message to the next consumer,
//     in sequence. On average every consumer will get the same number of
//     messages. This way of distributing messages is called round-robin.

// Command line 1: php worker.php => odd messages
// Command line 2: php worker.php => even messages

/** 2. Message acknowledgment */
//     In order to make sure a message is never lost, RabbitMQ supports
//     message acknowledgments. An ack(nowledgement) is sent back by the
//     consumer to tell RabbitMQ that a particular message has been received,
//     processed and that RabbitMQ is free to delete it.

$channel->close();
$connection->close();
