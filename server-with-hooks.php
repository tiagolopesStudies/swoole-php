<?php

use OpenSwoole\Http\Server;
use OpenSwoole\Http\Request;
use OpenSwoole\Http\Response;

Co::set(['hook_flags' => OpenSwoole\Runtime::HOOK_ALL]); // Habilita o uso de todos os tipos de Hooks do Swoole

$server = new Server('0.0.0.0', 8080);

$server->on('start', function () {
    echo 'OpenSwoole http server is started at http://localhost:8080';
});

$server->on('request', function (Request $request, Response $response) {
    $channelSize = 2;
    $channel     = new chan($channelSize);

    go(function () use ($channel) {
        $curl = curl_init('http://localhost:8001/server.php');
        curl_setopt(handle: $curl, option: CURLOPT_RETURNTRANSFER, value: true);
        $body = curl_exec($curl);

        $channel->push($body);
    });

    go(function () use ($channel) {
        $content = file_get_contents(__DIR__ . '/file.txt');
        $channel->push($content);
    });

    go(function () use ($channel, &$response) {
        $firstResponse  = $channel->pop();
        $secondResponse = $channel->pop();

        $response->end("$firstResponse $secondResponse");
    });
});

$server->start();
