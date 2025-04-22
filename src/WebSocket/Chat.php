<?php

namespace App\WebSocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Doctrine\ORM\EntityManagerInterface;

class Chat implements MessageComponentInterface
{
    protected $clients;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->clients = new \SplObjectStorage;
        $this->entityManager = $entityManager;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);
        $username = htmlspecialchars($data['username']);
        $content = htmlspecialchars($data['content']);

        // Zapisz wiadomość w bazie danych
        $user = $this->entityManager->getRepository(\App\Entity\User::class)->findOneBy(['username' => $username]);
        if ($user) {
            $message = new \App\Entity\Message();
            $message->setUser($user);
            $message->setContent($content);
            $message->setCreatedAt(new \DateTime());
            $this->entityManager->persist($message);
            $this->entityManager->flush();
        }

        // Wyślij wiadomość do wszystkich klientów
        foreach ($this->clients as $client) {
            $client->send(json_encode([
                'username' => $username,
                'content' => $content,
                'created_at' => date('Y-m-d H:i:s')
            ]));
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        echo "Connection closed! ({$conn->resourceId})\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error occurred: {$e->getMessage()}\n";
        $conn->close();
    }
}