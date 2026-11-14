<?php

namespace Concrete\Tests\Package;

use Concrete\Core\Package\PackageVersionComparator;
use Concrete\Tests\TestCase;

class PackageVersionComparatorTest extends TestCase
{
    /**
     * @dataProvider versionPairs
     */
    public function testIsNewer($available, $installed, $expected)
    {
        $this->assertSame($expected, PackageVersionComparator::isNewer($available, $installed));
    }

    public static function versionPairs()
    {
        return [
            'same version' => ['1.0.6', '1.0.6', false],
            'trailing zero in available' => ['1.0.6.0', '1.0.6', false],
            'multiple trailing zeros in available' => ['1.0.6.0.0', '1.0.6', false],
            'trailing zero in installed' => ['1.0.6', '1.0.6.0', false],
            'multiple trailing zeros in installed' => ['1.0.6', '1.0.6.0.0', false],
            'both with trailing zeros' => ['1.0.6.0.0', '1.0.6.0', false],
            'minor version upgrade' => ['1.1.0', '1.0.6', true],
            'patch version upgrade' => ['1.0.7', '1.0.6', true],
            'new nonzero segment' => ['1.0.6.1', '1.0.6', true],
            'new nonzero segment after zero' => ['1.0.6.0.1', '1.0.6', true],
            'upgrade with trailing zero' => ['1.0.7.0', '1.0.6', true],
            'short numeric version' => ['1.2', '1.2.0', false],
            'numeric downgrade' => ['1.0.5', '1.0.6', false],
            'prerelease upgrade' => ['1.0.6', '1.0.6RC1', true],
            'prerelease downgrade' => ['1.0.6RC1', '1.0.6', false],
            'prerelease remains untouched' => ['1.0.6.0', '1.0.6RC1', true],
            'empty update' => [null, '1.0.6', false],
            'all-zero versions' => ['0.0.0', '0', false],
            'padded zero in available' => ['1.0.6.00', '1.0.6.0', false],
            'padded zero in installed' => ['1.0.6.0', '1.0.6.000', false],
            'multiple padded zeros in available' => ['1.0.6.000.00', '1.0.6', false],
            'multiple padded zeros in installed' => ['1.0.6', '1.0.6.000.00', false],
            'nonzero padded segment is upgrade' => ['1.0.6.01', '1.0.6', true],
            'padded zero before nonzero is upgrade' => ['1.0.6.00.1', '1.0.6', true],
        ];
    }
}
