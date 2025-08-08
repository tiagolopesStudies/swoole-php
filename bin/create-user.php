<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Tiagolopes\SwoolePhp\Entity\Usuario;

$email = $argv[1];
$password = $argv[2];

/** @var ContainerInterface $container */
$container = require __DIR__ . '/../config/dependencias.php';

/** @var EntityManagerInterface $entityManager */
$entityManager = $container->get(EntityManagerInterface::class);
$user          = new Usuario();

$user->setEmail($email);
$user->setSenha($password);
$entityManager->persist($user);
$entityManager->flush();
