<?php

namespace Tiagolopes\SwoolePhp\Controller;

use Tiagolopes\SwoolePhp\Helper\HtmlViewTrait;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class LoginForm implements RequestHandlerInterface
{
    use HtmlViewTrait;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $html = $this->getHtmlFromTemplate('login/formulario.php', [
            'titulo' => 'Login'
        ]);
        return new Response(200, [], $html);
    }
}
