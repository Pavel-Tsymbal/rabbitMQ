<?php

require_once __DIR__ . '/../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare('jobs', false, false, false, false);

$jobData = [
    'user_id' => 1,
    'message' => 'test message',
    'operation_type' => 'test',
    'date' => date('d-m-Y H:m:s')
];

$msg = new AMQPMessage(
    json_encode($jobData, JSON_UNESCAPED_SLASHES),
    ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]
);

$channel->basic_publish($msg, '', 'jobs');

echo " [x] Sent 'job' \n";

$channel->close();
$connection->close();