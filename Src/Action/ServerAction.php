<?php
declare(strict_types=1);

namespace Src\Action;

use Sabre\DAV;
use Sabre\DAV\FS\Directory;
use Sabre\DAV\ICollection;
use Sabre\DAV\Server;
use Sabre\DAVACL\PrincipalBackend;
use Src\Db;

class ServerAction extends Action
{
    private $locksFile;

    public function __construct(Db $db, string $action, string $locksFile)
    {
        parent::__construct($db, $action);
        $this->locksFile = $locksFile;
    }

    protected function registerPlugins(Server $server): void
    {
        $server->addPlugin(new DAV\Locks\Plugin(new DAV\Locks\Backend\File($this->locksFile)));
    }

    protected function getServerConstructor(): ICollection
    {
        return new Directory('public');
    }
}
