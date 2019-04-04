<?php
declare(strict_types=1);

namespace Src\Action;

use Src\Db;
use Src\ServerException;

class Factory
{
    /** @var Db */
    private $db;
    /** @var string */
    private $locksFile;
    /** @var string */
    private $publicPath;

    public function __construct(Db $db, string $locksFile, string $publicPath)
    {
        $this->db = $db;
        $this->locksFile = $locksFile;
        $this->publicPath = $publicPath;
    }

    /**
     * @param string $type
     * @return Action
     * @throws ServerException
     */
    public function get(string $type): Action
    {
        switch ($type) {
            case Actions::CALENDARS:
                return new Calendars($this->db, $type);
            case Actions::CONTACTS:
                return new Contacts($this->db, $type);
            case Actions::SERVER:
                return new ServerAction($this->db, $type, $this->locksFile, $this->publicPath);
        }
        throw new ServerException("Invalid action '$type'");
    }

    private function getParams(string $baseUri): array
    {
        return [
            $this->db,
            $baseUri,
        ];
    }
}
