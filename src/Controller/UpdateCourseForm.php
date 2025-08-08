<?php

namespace Tiagolopes\SwoolePhp\Controller;

use Tiagolopes\SwoolePhp\Helper\HtmlViewTrait;
use Tiagolopes\SwoolePhp\Entity\Curso;
use Doctrine\ORM\EntityManagerInterface;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class UpdateCourseForm implements RequestHandlerInterface
{
    use HtmlViewTrait;

    public function __construct(private EntityManagerInterface $entityManager)
    { }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $curso = $this->entityManager->find(Curso::class, $request->getQueryParams()['id']);

        $titulo = 'Editar Curso';
        $html = $this->getHtmlFromTemplate('cursos/formulario.php', compact('curso', 'titulo'));

        return new Response(200, [], $html);
    }
}
