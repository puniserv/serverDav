<?php
declare(strict_types=1);

namespace Spec\App;

use Sabre\DAV\Server;
use Src\App;
use Src\ServerException;

describe(App::class, function () {
    it('should throw exception about timezone', function () {
        runAppAndExpectException([], 'Config "timezone" missing');
    });
    it('should throw exception about missing dsn config', function () {
        runAppAndExpectException([
            'timezone' => 'Canada/Eastern',
        ], 'Config "services->db->dsn" missing');
    });
    it('should redirect to default action', function () {
        $_SERVER['REQUEST_URI'] = '';
        allow('header')->toBeCalled();
        expect('header')->toBeCalled()->with('Location: /server/')->times(1);
        runApp([
            'timezone' => 'Canada/Eastern',
            'services' => [
                'db' => [
                    'dsn' => 'sqlite:' . getTestDbFile(),
                ]
            ],
        ]);
    });
    it('should exec server action', function () {
        $_SERVER['REQUEST_URI'] = '/server/';
        checkAction('/server/');
    });

    it('should exec server action', function () {
        $_SERVER['REQUEST_URI'] = '/calendars/';
        checkAction('/calendars/');
    });

    it('should exec server action', function () {
        $_SERVER['REQUEST_URI'] = '/contacts/';
        checkAction('/contacts/');
    });

    function checkAction(string $expectedBaseUri): void
    {
        allow('header')->toBeCalled();
        allow(Server::class)->toReceive('__construct');
        allow(Server::class)->toReceive('exec');
        allow(Server::class)->toReceive('setBaseUri');
        allow(Server::class)->toReceive('addPlugin');
        expect('header')->not->toBeCalled();
        expect(Server::class)->toReceive('exec')->times(1);
        expect(Server::class)->toReceive('setBaseUri')->with($expectedBaseUri);
        runApp([
            'timezone' => 'Canada/Eastern',
            'services' => [
                'db' => [
                    'dsn' => 'sqlite:' . getTestDbFile(),
                ]
            ],
        ]);
    }

    function getTestDbFile(): string
    {
        $path = __DIR__ . '/resources/test.sqlite';
        file_put_contents($path, '');
        return $path;
    }

    function runAppAndExpectException(array $config, string $message): void
    {
        expect(function () use ($config) {
            runApp($config);
        })->toThrow(new ServerException($message));
    }

    function runApp(array $config): void
    {
        (new App($config))->run();
    }
});
