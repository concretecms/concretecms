<?php

declare(strict_types=1);

namespace Concrete\Core\User\Search\ColumnSet\Column;

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\Search\MembershipsProvider;
use Concrete\Core\User\UserInfo;

defined('C5_EXECUTE') or die('Access Denied.');

final class MembershipsColumnFormatter
{
    public static function getDirectMembershipsCount(UserInfo $userInfo): int
    {
        return self::getProvider()->getDirectMembershipsCount((int) $userInfo->getUserID());
    }

    public static function getDirectMemberships(UserInfo $userInfo): string
    {
        $names = array_map(
            static function (string $name): string {
                return tc('GroupName', $name);
            },
            self::getProvider()->getDirectGroupNames((int) $userInfo->getUserID())
        );
        natcasesort($names);

        return self::formatBadges(array_map('h', $names), 'ccm-user-memberships-direct');
    }

    public static function getIndirectMemberships(UserInfo $userInfo): string
    {
        $paths = array_map(static function (array $path): array {
            return array_map(static function (string $name): string {
                return tc('GroupName', $name);
            }, $path);
        }, self::getProvider()->getIndirectGroupPaths((int) $userInfo->getUserID()));
        usort($paths, static function (array $a, array $b): int {
            $comparison = strnatcasecmp($a[count($a) - 1], $b[count($b) - 1]);
            if ($comparison !== 0) {
                return $comparison;
            }

            return strnatcasecmp(implode("\0", $a), implode("\0", $b));
        });

        return self::formatBadges(array_map(static function (array $path): string {
            return implode(' &gt; ', array_map('h', $path));
        }, $paths), 'ccm-user-memberships-indirect');
    }

    private static function formatBadges(array $labels, string $class): string
    {
        if ($labels === []) {
            return '';
        }

        return sprintf(
            '<div class="d-flex flex-wrap gap-1 %s">%s</div>',
            $class,
            implode('', array_map(static function (string $label): string {
                return sprintf(
                    '<span class="ccm-user-membership-chain">'
                    . '<span class="badge bg-light text-dark border text-wrap text-start">%s</span>'
                    . '</span>',
                    $label
                );
            }, $labels))
        );
    }

    private static function getProvider(): MembershipsProvider
    {
        $app = Application::getFacadeApplication();

        /** @var MembershipsProvider $provider */
        return $app->make(MembershipsProvider::class);
    }
}
