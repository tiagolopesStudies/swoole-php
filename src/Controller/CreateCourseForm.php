<?php

namespace Tiagolopes\SwoolePhp\Controller;

use Tiagolopes\SwoolePhp\Helper\HtmlViewTrait;
use Tiagolopes\SwoolePhp\Entity\Curso;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CreateCourseForm implements RequestHandlerInterface
{
    use HtmlViewTrait;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $curso = new Curso();
        $titulo = 'Cadastrar Curso';
        $html = $this->getHtmlFromTemplate('cursos/formulario.php', compact('curso', 'titulo'));

        return new Response(200, [], $html);
    }
}
