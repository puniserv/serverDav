<?php

use Src\Container;

return [
    'timezone' => getenv('DAV_TIMEZONE') ?: 'Canada/Eastern',
    'sqlLiteFilePath' => $sqliteFilePath = __DIR__ . '/../data/db.sqlite',
    'services' => [
        Container::DB => [
            'dsn' => 'sqlite:' . $sqliteFilePath,
        ],
        Container::ACTION_FACTORY => [
            'locksFile' => __DIR__ . '/../data/locks',
            'publicPath' => __DIR__ . '/../web/public',
        ],
    ],
];
