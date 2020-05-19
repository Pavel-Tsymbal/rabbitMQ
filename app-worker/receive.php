<?php
require_once __DIR__ . '/../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$host = 'mariadb';
$db   = 'dbname';
$user = 'root';
$pass = 'mysql';
$charset = 'utf8';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$pdo = new PDO($dsn, $user, $pass, $opt);

$sql =
    'INSERT INTO jobs (message, email, executed, operation_type) VALUES (:message, :email, :executed, :operation_type)';
$stmt = $pdo->prepare($sql);

$connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare('jobs', false, false, false, false);

echo " [*] Waiting for a job. To exit press CTRL+C\n";

$messages = [];
$callback = function ($msg) use ($stmt, &$messages) {
    $messages[] = json_decode($msg->body, true);
    $counter = 5;

    if (count($messages) === $counter) {
        foreach ($messages as $msg) {
            $stmt->execute([
                'message' => $msg['message'],
                'email' => $msg['email'],
                'operation_type' => $msg['operation_type'],
                'executed' => $msg['date']
            ]);
        }

        echo 'Processed new messages: ', $counter, "\n";
        $messages = [];
    }
};

$channel->basic_consume('jobs', '', false, false, false, false, $callback);

while ($channel->is_consuming()) {
    $channel->wait();
}