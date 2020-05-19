<?php

require_once __DIR__ . '/../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare('jobs', false, false, false, false);

$jobData = [
    'email' => $_POST['email'],
    'message' => $_POST['message'],
    'operation_type' => 'test',
    'date' => date('d-m-Y H:m:s')
];

$msg = new AMQPMessage(
    json_encode($jobData, JSON_UNESCAPED_SLASHES),
    ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]
);

$channel->basic_publish($msg, '', 'jobs');

$channel->close();
$connection->close();

header('Location: http://localhost:2222/send_view.php');