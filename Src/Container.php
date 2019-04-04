<?php
declare(strict_types=1);

namespace Src;

use Src\Action\Factory;

class Container
{
    public const ACTION_FACTORY = 'actionFactory';
    public const DB = 'db';
    public const REQUEST = 'request';
    /** @var App */
    private $app;
    private $instances = [];

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    public function getActionFactory(): Factory
    {
        return $this->instances[self::ACTION_FACTORY] ?? $this->instances[self::ACTION_FACTORY] = new Factory(
                $this->getDb(),
                $this->app->getFactoryConfig()['locksFile'] ?? '',
                $this->app->getFactoryConfig()['publicPath'] ?? ''
            );
    }

    public function getDb(): Db
    {
        return $this->instances[self::DB] ?? $this->instances[self::DB] = new Db($this->app->getDbConfig());
    }

    public function getRequest(): Request
    {
        return $this->instances[self::REQUEST] ?? $this->instances[self::REQUEST] = new Request($this->getActionFactory());
    }
}
