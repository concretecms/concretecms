<?php

declare(strict_types=1);

namespace Concrete\Core\User\Group;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Evaluates the same rules as Group::isUserExpired() from already loaded membership data.
 */
final class MembershipExpirationChecker
{
    public function isExpired(
        bool $enabled,
        ?string $method,
        ?string $setDateTime,
        int $intervalMinutes,
        ?string $enteredDateTime,
        ?int $now = null
    ): bool {
        if (!$enabled) {
            return false;
        }

        $now = $now ?? time();
        switch ($method) {
            case 'SET_TIME':
                return $now > (int) strtotime((string) $setDateTime);
            case 'INTERVAL':
                return $now > (int) strtotime((string) $enteredDateTime) + ($intervalMinutes * 60);
        }

        return false;
    }
}
