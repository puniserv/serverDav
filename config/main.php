<?php

use Src\Container;

return [
    'timezone' => getenv('DAV_TIMEZONE') ?: 'Canada/Eastern',
    'services' => [
        Container::DB => [
            'dsn' => 'sqlite:' . __DIR__ . '/../data/db.sqlite',
        ],
        Container::ACTION_FACTORY => [
            'locksFile' => __DIR__ . '/../data/locks',
            'publicPath' => __DIR__ . '/../web/public',
        ],
    ],
];
