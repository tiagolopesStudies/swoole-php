<?php

use Tiagolopes\SwoolePhp\Controller;

return [
    '/login' => Controller\LoginForm::class,
    '/fazer-login' => Controller\Login::class,
    '/logout' => Controller\Logout::class,
    '/novo-curso' => Controller\CreateCourseForm::class,
    '/salvar-curso' => Controller\StoreCourse::class,
    '/listar-cursos' => Controller\CourseList::class,
    '/editar-curso' => Controller\UpdateCourseForm::class,
    '/excluir-curso' => Controller\DeleteCourse::class,
];