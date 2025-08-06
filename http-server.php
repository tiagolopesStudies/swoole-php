<?php

use OpenSwoole\Http\Server;
use OpenSwoole\Http\Request;
use OpenSwoole\Http\Response;

$server = new Server('0.0.0.0', 8080);

$server->on('start', function (Server $server) {
    echo 'OpenSwoole http server is started at http://localhost:8080';
});

$server->on('request', function (Request $request, Response $response) {
    $response->header('Content-Type', 'text/plain');
    $response->end("Hello World\n");
});

$server->start();
