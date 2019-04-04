<?php
declare(strict_types=1);

namespace Src\Action;

use Sabre\DAV;
use Sabre\DAV\ICollection;
use Sabre\DAV\Server;
use Sabre\DAVACL;
use Sabre\DAVACL\PrincipalBackend;
use Src\ServerException;

abstract class ContactsCalendars extends Action
{
    abstract protected function getMainNode(PrincipalBackend\PDO $principalBackend): ICollection;

    protected function registerCommonPlugins(Server $server): void
    {
        parent::registerCommonPlugins($server);
        $server->addPlugin(new DAV\Sync\Plugin());
        $server->addPlugin(new DAVACL\Plugin());
    }

    /**
     * @return array
     * @throws ServerException
     */
    protected function getServerConstructor(): array
    {
        $principalBackend = $this->getBackendPDO();
        return [
            new DAVACL\PrincipalCollection($principalBackend),
            $this->getMainNode($principalBackend),
        ];
    }

    /**
     * @return PrincipalBackend\PDO
     * @throws ServerException
     */
    protected function getBackendPDO(): PrincipalBackend\PDO
    {
        return new PrincipalBackend\PDO($this->getPdo());
    }
}
