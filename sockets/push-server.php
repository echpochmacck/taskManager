<?php
require __DIR__ . '/../vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use React\EventLoop\Factory;
use React\Socket\SocketServer;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

class Pusher implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "Новое подключение: {$conn->resourceId}\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        // входящие сообщения от клиента можно игнорировать
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        echo "Отключился: {$conn->resourceId}\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Ошибка: {$e->getMessage()}\n";
        $conn->close();
    }

    public function broadcast($message) {
        foreach ($this->clients as $client) {
            $client->send($message);
        }
    }
}

$loop = Factory::create();
$pusher = new Pusher();

// Redis
$redis = new \Redis();
$redis->connect('127.127.126.56', 6379);

// WebSocket
$socket = new SocketServer('0.0.0.0:8080', [], $loop);
$server = new IoServer(
    new HttpServer(new WsServer($pusher)),
    $socket,
    $loop
);

// Проверка Redis каждую секунду
$loop->addPeriodicTimer(1, function () use ($redis, $pusher) {
    $msg = $redis->lPop('blog_channel');
    if ($msg) {
        echo "Отправка: $msg\n";
        $pusher->broadcast($msg);
    }
});

$loop->run();
