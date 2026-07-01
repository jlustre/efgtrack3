<?php

namespace App\Support;

class SystemTaskAssignor
{
    public const USER_ID = 999;

    public const NAME = 'System';

    public const EMAIL = 'system@efgtrack.internal';

    public static function id(): int
    {
        return self::USER_ID;
    }

    public static function isSystemAssignor(?int $assignorId): bool
    {
        return (int) $assignorId === self::USER_ID;
    }
}
