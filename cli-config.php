<?php

use Tiagolopes\SwoolePhp\Infra\EntityManagerCreator;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

require_once __DIR__ . '/vendor/autoload.php';

return ConsoleRunner::createHelperSet(
    (new EntityManagerCreator())->getEntityManager()
);
