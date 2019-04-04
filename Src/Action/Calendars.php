<?php
declare(strict_types=1);

namespace Src\Action;

use Sabre\CalDAV;
use Sabre\CalDAV\Backend;
use Sabre\CalDAV\CalendarRoot;
use Sabre\DAV\ICollection;
use Sabre\DAV\Server;
use Sabre\DAV\Sharing;
use Sabre\DAVACL\PrincipalBackend;
use Src\ServerException;

class Calendars extends ContactsCalendars
{
    protected function registerPlugins(Server $server): void
    {
        $server->addPlugin(new Sharing\Plugin());
        $server->addPlugin(new CalDAV\Plugin());
        $server->addPlugin(new CalDAV\Subscriptions\Plugin());
        $server->addPlugin(new CalDAV\Schedule\Plugin());
        $server->addPlugin(new CalDAV\SharingPlugin());
    }

    /**
     * @param PrincipalBackend\PDO $principalBackend
     * @return ICollection
     * @throws ServerException
     */
    protected function getMainNode(PrincipalBackend\PDO $principalBackend): ICollection
    {
        return new CalendarRoot(
            $principalBackend,
            new Backend\PDO($this->getPdo())
        );
    }
}
