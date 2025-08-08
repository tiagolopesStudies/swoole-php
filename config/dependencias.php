<?php

use Tiagolopes\SwoolePhp\Infra\EntityManagerCreator;
use DI\ContainerBuilder;
use Doctrine\ORM\EntityManagerInterface;

$builder = new ContainerBuilder();

$builder->addDefinitions([
    EntityManagerInterface::class => fn () => (new EntityManagerCreator())->getEntityManager(),
]);

return $builder->build();
