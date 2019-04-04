<?php
declare(strict_types=1);

namespace Src\Action;

use Sabre\CardDAV\AddressBookRoot;
use Sabre\CardDAV\Backend;
use Sabre\CardDAV\Plugin;
use Sabre\DAV\ICollection;
use Sabre\DAV\Server;
use Sabre\DAVACL\PrincipalBackend;
use Src\ServerException;

class Contacts extends ContactsCalendars
{
    protected function registerPlugins(Server $server): void
    {
        $server->addPlugin(new Plugin());
    }

    /**
     * @param PrincipalBackend\PDO $principalBackend
     * @return ICollection
     * @throws ServerException
     */
    protected function getMainNode(PrincipalBackend\PDO $principalBackend): ICollection
    {
        return new AddressBookRoot(
            $principalBackend,
            new Backend\PDO($this->getPdo())
        );
    }
}
