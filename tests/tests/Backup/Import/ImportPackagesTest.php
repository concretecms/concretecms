<?php

declare(strict_types=1);

namespace Concrete\Tests\Backup\Import;

use Concrete\Core\Backup\ContentImporter\Importer\Routine\ImportPackagesRoutine;
use Concrete\Core\Package\Package;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Utility\Service\Xml;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Tests\TestCase;
use Mockery as M;

class ImportPackagesTest extends TestCase
{
    public static function provideTestCases(): array
    {
        return [
            [
                '<package handle="test_package" />',
                [
                ],
            ],
            [
                '<package handle="test_package" full-content-swap="false" />',
                [
                ],
            ],
            [
                '<package handle="test_package" full-content-swap="" />',
                [
                ],
            ],
            [
                '<package handle="test_package" full-content-swap="0" />',
                [
                ],
            ],
            [
                '<package handle="test_package" full-content-swap="no" />',
                [
                ],
            ],
            [
                '<package handle="test_package" content-swap-file="my-content.xml" />',
                [
                ],
            ],
            [
                '<package handle="test_package" full-content-swap="true" />',
                [
                    'pkgDoFullContentSwap' => true,
                    'ccm_token' => 'GeneratedToken',
                    'contentSwapFile' => 'content.xml',
                ],
            ],
            [
                '<package handle="test_package" full-content-swap="1" />',
                [
                    'pkgDoFullContentSwap' => true,
                    'ccm_token' => 'GeneratedToken',
                    'contentSwapFile' => 'content.xml',
                ],
            ],
            [
                '<package handle="test_package" full-content-swap="yes" content-swap-file="my-content.xml" />',
                [
                    'pkgDoFullContentSwap' => true,
                    'ccm_token' => 'GeneratedToken',
                    'contentSwapFile' => 'my-content.xml',
                ],
            ],
            [
                '<package handle="test_package">
                    <option name="opt" />
                </package>',
                [
                    'opt' => '',
                ],
            ],
            [
                '<package handle="test_package">
                    <option name="opt" value="val" />
                </package>',
                [
                    'opt' => 'val',
                ],
            ],
            [
                '<package handle="test_package">
                    <option name="opt[]" value="val" />
                </package>',
                [
                    'opt' => ['val'],
                ],
            ],
            [
                '<package handle="test_package">
                    <option name="opt[]" value="val1" />
                    <option name="opt[]" value="val2" />
                </package>',
                [
                    'opt' => ['val1', 'val2'],
                ],
            ],
            [
                '<package handle="test_package" full-content-swap="true" content-swap-file="my-content.xml" >
                    <option name="arr1[]" value="val1" />
                    <option name="arr2[]" value="val21" />
                    <option name="plain" value="value" />
                    <option name="arr2[]" value="val22" />
                </package>',
                [
                    'pkgDoFullContentSwap' => true,
                    'ccm_token' => 'GeneratedToken',
                    'contentSwapFile' => 'my-content.xml',
                    'arr1' => ['val1'],
                    'arr2' => ['val21', 'val22'],
                    'plain' => 'value',
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideTestCases
     */
    public function testReadingCIF(string $cifChunk, array $expectedInstallOptions): void
    {
        $packageControllerMock = M::mock(Package::class);

        $tokenMock = M::mock(Token::class);
        $tokenMock->shouldReceive('generate')->zeroOrMoreTimes()->with('install_options_selected')->andReturn('GeneratedToken');

        $packageServiceMock = M::mock(PackageService::class);
        $packageServiceMock->shouldReceive('getByHandle')->once()->with('test_package')->andReturn(null);
        $packageServiceMock->shouldReceive('getClass')->once()->with('test_package')->andReturn($packageControllerMock);
        $packageServiceMock->shouldReceive('install')->once()->with($packageControllerMock, $expectedInstallOptions);

        $sx = simplexml_load_string('<?xml version="1.0"?><concrete5-cif version="1.0"><packages>' . $cifChunk . '</packages></concrete5-cif>');
        $importer = new ImportPackagesRoutine($packageServiceMock, app(Xml::class), $tokenMock);
        $importer->import($sx);
    }
}
