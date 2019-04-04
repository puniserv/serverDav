<?php
declare(strict_types=1);

namespace Src;

use Src\Action\Factory;

class Container
{
    public const ACTION_FACTORY = 'actionFactory';
    public const DB = 'db';
    public const REQUEST = 'request';
    private $config;
    private $instances = [];

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getActionFactory(): Factory
    {
        return $this->instances[self::ACTION_FACTORY] ?? $this->instances[self::ACTION_FACTORY] = new Factory(
                $this->getDb(),
                $this->config[self::ACTION_FACTORY]['locksFile'] ?? '',
                $this->config[self::ACTION_FACTORY]['publicPath'] ?? ''
            );
    }

    public function getDb(): Db
    {
        return $this->instances[self::DB] ?? $this->instances[self::DB] = new Db($this->config[self::DB] ?? []);
    }

    public function getRequest(): Request
    {
        return $this->instances[self::REQUEST] ?? $this->instances[self::REQUEST] = new Request($this->getActionFactory());
    }
}
