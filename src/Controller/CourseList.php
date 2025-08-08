<?php

namespace Tiagolopes\SwoolePhp\Controller;

use Tiagolopes\SwoolePhp\Helper\HtmlViewTrait;
use Tiagolopes\SwoolePhp\Entity\Curso;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CourseList implements RequestHandlerInterface
{
    use HtmlViewTrait;

    private ObjectRepository $locaisRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->locaisRepository = $entityManager->getRepository(Curso::class);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $cursos = $this->locaisRepository->findBy($request->getQueryParams(), ['descricao' => 'ASC']);
        $titulo = 'Listagem de Cursos';

        $html = $this->getHtmlFromTemplate('cursos/listar.php', compact('cursos', 'titulo'));

        return new Response(200, [], $html);
    }
}
