<?php

namespace Sockets;

use Ratchet\ConnectionInterface;
use Ratchet\Wamp\WampServerInterface;

class Pusher implements WampServerInterface
{
    protected $subscribedTopics = [];

    public function onSubscribe(ConnectionInterface $conn, $topic)
    {
        $this->subscribedTopics[$topic->getId()] = $topic;
    }

    public function onUnSubscribe(ConnectionInterface $conn, $topic) {}
    public function onOpen(ConnectionInterface $conn) {}
    public function onClose(ConnectionInterface $conn) {}
    public function onCall(ConnectionInterface $conn, $id, $topic, array $params)
    {
        $conn->callError($id, $topic, 'Вызовы запрещены')->close();
    }
    public function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible)
    {
        $conn->close();
    }
    public function onError(ConnectionInterface $conn, \Exception $e) {}

    // Обработка новых блогов из Redis
    public function onBlogEntry($message)
    {
        $entryData = json_decode($message, true);
        if (!isset($this->subscribedTopics[$entryData['category']])) {
            return;
        }
        $topic = $this->subscribedTopics[$entryData['category']];
        $topic->broadcast($entryData);
    }
}
