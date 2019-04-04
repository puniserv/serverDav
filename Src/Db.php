<?php
declare(strict_types=1);

namespace Src;

class Db
{
    private $pdo;
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function connect(): self
    {
        $this->pdo = new \PDO($this->config['dsn'] ?? '');
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        return $this;
    }

    public function close(): void
    {
        $this->pdo = null;
    }

    /**
     * @return \PDO
     * @throws ServerException
     */
    public function getPdo(): \PDO
    {
        if(!$this->pdo){
            throw new ServerException('Database is not connected');
        }
        return $this->pdo;
    }
}
