<?php

use Tiagolopes\SwoolePhp\Controller\CourseList;
use Tiagolopes\SwoolePhp\Controller\CreateCourseForm;
use Tiagolopes\SwoolePhp\Controller\DeleteCourse;
use Tiagolopes\SwoolePhp\Controller\Login;
use Tiagolopes\SwoolePhp\Controller\LoginForm;
use Tiagolopes\SwoolePhp\Controller\Logout;
use Tiagolopes\SwoolePhp\Controller\StoreCourse;
use Tiagolopes\SwoolePhp\Controller\UpdateCourseForm;

return [
    '/login' => LoginForm::class,
    '/fazer-login' => Login::class,
    '/logout' => Logout::class,
    '/novo-curso' => CreateCourseForm::class,
    '/salvar-curso' => StoreCourse::class,
    '/listar-cursos' => CourseList::class,
    '/editar-curso' => UpdateCourseForm::class,
    '/excluir-curso' => DeleteCourse::class,
];