<?php
declare(strict_types=1);

namespace Src\Action;

class Actions
{
    public const CALENDARS = 'calendars';
    public const SERVER = 'server';
    public const CONTACTS = 'contacts';

    public const ALL_ACTIONS = [
        self::CALENDARS,
        self::SERVER,
        self::CONTACTS,
    ];

    public static function checkIfActionExist(string $action): bool
    {
        return in_array($action, self::ALL_ACTIONS, true);
    }
}
