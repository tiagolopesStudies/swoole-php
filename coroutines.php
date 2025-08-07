<?php

require_once __DIR__ . '/vendor/autoload.php';

co::run(function () {
    go(function () {
        co::sleep(2);
        echo 'Show after 2 seconds' . PHP_EOL;
    });

    go(function () {
        co::sleep(1);
        echo 'Show after 1 second' . PHP_EOL;
    });
});
