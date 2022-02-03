<?php

require_once __DIR__ . '../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

$severity = isset($argv[1]) && !empty($argv[1]) ? $argv[1] : 'info';

echo $severity;

$channel->queue_declare('my_new_queue', false, true, false, false, false, new AMQPTable(['x-queue-type' => 'quorum']));


$data = implode(' ', array_slice($argv, 1));

if (empty($data)) {
    $data = "Hello World!";
}

// MARCAMOS EL MENSAJE COMO modo de entrega persistente
$msg = new AMQPMessage(
    $data,
    //MARCAMOS MENSAJES COMO PERSISTENTES
    array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT)
);

$channel->basic_publish($msg, '', 'my_new_queue');

echo ' [x] Sent ', $data, "\n";

$channel->close();

$connection->close();

