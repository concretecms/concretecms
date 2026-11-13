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
            $available = self::withoutTrailingZeros($available);
            $installed = self::withoutTrailingZeros($installed);
        }

        return version_compare((string) $available, (string) $installed, '>');
    }

    private static function isNumericVersion($version)
    {
        return is_string($version) && preg_match('/\A[0-9]+(?:\.[0-9]+)*\z/', $version) === 1;
    }

    private static function withoutTrailingZeros($version)
    {
        $parts = explode('.', $version);
        while (count($parts) > 1 && end($parts) === '0') {
            array_pop($parts);
        }

        return implode('.', $parts);
    }
}
