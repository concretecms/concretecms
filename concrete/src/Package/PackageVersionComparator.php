<?php

namespace Concrete\Core\Package;

/**
 * Compares package versions without treating trailing numeric zero segments as updates.
 */
final class PackageVersionComparator
{
    public static function isNewer($available, $installed)
    {
        if (self::isNumericVersion($available) && self::isNumericVersion($installed)) {
            $available = preg_replace('/(?:\.0+)+\z/', '', (string) $available);
            $installed = preg_replace('/(?:\.0+)+\z/', '', (string) $installed);
        }

        return version_compare((string) $available, (string) $installed, '>');
    }

    private static function isNumericVersion($version)
    {
        return is_string($version) && preg_match('/\A[0-9]+(?:\.[0-9]+)*\z/', $version) === 1;
    }
}
