<?php

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use App\WebSocket\Chat;
use Symfony\Component\DependencyInjection\ContainerBuilder;

require dirname(__DIR__) . '/vendor/autoload.php';

$kernel = new \App\Kernel('dev', true);
$kernel->boot();
$container = $kernel->getContainer();

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Chat($container->get('doctrine.orm.entity_manager'))
        )
    ),
    8080
);

$server->run();