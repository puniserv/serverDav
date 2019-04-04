<?php
declare(strict_types=1);

namespace Src\Action;

use PDO;
use Src\Db;
use Src\ServerException;
use Sabre\DAV;
use Sabre\DAV\Server;
use Sabre\DAV\Exception;
use Sabre\DAV\Auth\Backend;

abstract class Action
{
    /** @var Db */
    private $db;
    private $action;
    /** @var PDO */
    private $pdo;

    abstract protected function registerPlugins(Server $server): void;
    abstract protected function getServerConstructor();

    public function __construct(Db $db, string $action)
    {
        $this->db = $db;
        $this->action = $action;
    }

    /**
     * @throws ServerException
     */
    public function run(): void
    {
        try{
            $server = $this->createServer();
            $this->registerCommonPlugins($server);
            $this->registerPlugins($server);
            $server->exec();
        }catch (\Throwable $throwable){
            throw new ServerException($throwable->getMessage());
        }
    }

    protected function getBaseUri(): string
    {
        return '/' . $this->action . '/';
    }


    /**
     * @return Server
     * @throws Exception
     */
    protected function createServer(): Server
    {
        $server = new Server($this->getServerConstructor());
        $server->setBaseUri($this->getBaseUri());
        return $server;
    }

    /**
     * @return PDO
     * @throws ServerException
     */
    protected function getPdo(): PDO
    {
        return $this->pdo ?? $this->pdo = $this->db->getPdo();
    }

    /**
     * @return Backend\PDO
     * @throws ServerException
     */
    protected function getAuthPDO(): Backend\PDO
    {
        $authentication = new Backend\PDO($this->getPdo());
        $authentication->setRealm('SabreDAV');
        return $authentication;
    }

    /**
     * @param Server $server
     * @throws ServerException
     */
    protected function registerCommonPlugins(Server $server): void
    {
        $server->addPlugin(new DAV\Auth\Plugin($this->getAuthPDO()));
        $server->addPlugin(new DAV\Browser\Plugin());
    }
}
