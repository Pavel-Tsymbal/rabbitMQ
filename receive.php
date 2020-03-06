<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$host = '127.0.0.1';
$db   = 'rabbit';
$user = 'root';
$pass = '';
$charset = 'utf8';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
$pdo = new PDO($dsn, $user, $pass, $opt);

$sql = 'INSERT INTO jobs (message, user_id, executed, operation_type) VALUES (:message, :user_id, :executed, :operation_type)';
$stmt = $pdo->prepare($sql);

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare('jobs', false, false, false, false);

echo " [*] Waiting for a job. To exit press CTRL+C\n";


$callback = function ($msg) use ($stmt){
    $msg = json_decode($msg->body, $assocForm=true);

    $stmt->execute([
        'message' => $msg['message'],
        'user_id' => $msg['user_id'],
        'operation_type' => $msg['operation_type'],
        'executed' => $msg['date']
    ]);

    echo ' [x] Message ', $msg['message'], "\n";
    echo ' [x] User id ', $msg['user_id'], "\n";
    echo ' [x] Operation type ', $msg['operation_type'], "\n";
    echo ' [x] Date ', $msg['date'], "\n";
};

$channel->basic_consume('jobs', '', false, false, false, false, $callback);

while ($channel->is_consuming()) {
    $channel->wait();
}