<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Nyholm\Psr7\ServerRequest;
use OpenSwoole\Http\Server;
use OpenSwoole\Http\Request;
use OpenSwoole\Http\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;

ini_set('error_reporting', E_ALL);

Co::set(['hook_flags' => OpenSwoole\Runtime::HOOK_ALL]);

/** @var array<string, string> $rotas */
$rotas = require __DIR__ . '/../config/rotas.php';
/** @var ContainerInterface $container */
$container = require __DIR__ . '/../config/dependencias.php';

$server = new Server('0.0.0.0', 8080);

$server->on('start', function () {
    echo 'OpenSwoole http server is started at http://localhost:8080';
});

$server->on('request', function (Request $request, Response $response) use ($rotas, $container) {
    $path = $request->server['path_info'] ?? '/';

    if ($path === '/') {
        $response->redirect('/listar-cursos');
        return;
    }

    if (! isset($rotas[$path])) {
        $response->status(404);
        return;
    }

    if (
        session_status() === PHP_SESSION_ACTIVE
        && array_key_exists(session_name(), $request->cookie)
        && session_id() !== $request->cookie[session_name()]
    ) {
        session_abort();
        session_id($request->cookie[session_name()]);
    }

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (!isset($_SESSION['logado']) && stripos($path, 'login') === false) {
        $_SESSION['tipo_mensagem'] = 'danger';
        $_SESSION['mensagem_flash'] = 'Você não está logado';
        $response->redirect('/login');
        return;
    }

    $controllerClass = $rotas[$path];

    $serverRequest = new ServerRequest(
        method: $request->getMethod(),
        uri: $request->server['request_uri'],
        headers: $request->header,
        body: $request->getData(),
        version: '1.1',
        serverParams: $request->server
    )
        ->withQueryParams($request->get ?? [])
        ->withParsedBody($request->post ?? []);

    /** @var RequestHandlerInterface $controllerInstance */
    $controllerInstance = $container->get($controllerClass);

    $result = $controllerInstance->handle($serverRequest);

    foreach ($result->getHeaders() as $header => $valores) {
        if ($header === 'Location') {
            $response->redirect($valores[0]);
            return;
        }

        foreach ($valores as $value) {
            $response->header($header, $value);
        }
    }
    $response->end($result->getBody()->getContents());
});

$server->start();
