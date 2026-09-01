<?php

declare(strict_types=1);

namespace Concrete\Tests\Backup;

use Concrete\Core\Backup\ContentExporterOptions;
use Concrete\Core\Http\Request;
use Concrete\Tests\TestCase;

defined('C5_EXECUTE') or die('Access Denied.');

class ContentExporterOptionsTest extends TestCase
{
    public static function providerExportIDs(): array
    {
        return [
            'regular request' => ['/dashboard/files/search', false],
            'regular request with the query string parameter' => ['/dashboard/files/search?export_ids=1', false],
            'API request' => ['/ccm/api/1.0/pages/1', true],
            'API request (version without minor)' => ['/ccm/api/1/pages/1', true],
            'API request (version with patch)' => ['/ccm/api/1.0.3/pages/1', true],
            'API request with an empty parameter' => ['/ccm/api/1.0/pages/1?export_ids=', true],
            'API request with the parameter turned on' => ['/ccm/api/1.0/pages/1?export_ids=1', true],
            'API request with the parameter turned off' => ['/ccm/api/1.0/pages/1?export_ids=0', false],
            'API endpoint list' => ['/ccm/api/1.0', false],
        ];
    }

    /**
     * @dataProvider providerExportIDs
     */
    public function testExportIDsIsDetectedFromTheRequest(string $url, bool $expected): void
    {
        $options = new ContentExporterOptions(Request::create($url));

        static::assertSame($expected, $options->isExportIDs());
    }

    public static function providerExportFilesAsUUID(): array
    {
        return [
            'regular request' => ['/dashboard/files/search', false],
            'API request' => ['/ccm/api/1.0/pages/1', true],
            // it doesn't depend on the export_ids parameter
            'API request with IDs turned off' => ['/ccm/api/1.0/pages/1?export_ids=0', true],
        ];
    }

    /**
     * @dataProvider providerExportFilesAsUUID
     */
    public function testExportFilesAsUUIDIsDetectedFromTheRequest(string $url, bool $expected): void
    {
        $options = new ContentExporterOptions(Request::create($url));

        static::assertSame($expected, $options->isExportFilesAsUUID());
    }

    public function testExportFilesWithoutPrefixIsTurnedOffByDefault(): void
    {
        $options = new ContentExporterOptions(Request::create('/ccm/api/1.0/pages/1'));

        static::assertFalse($options->isExportFilesWithoutPrefix());
    }

    public function testOptionsCanBeOverridden(): void
    {
        $options = new ContentExporterOptions(Request::create('/dashboard/files/search'));

        static::assertSame($options, $options->setExportIDs(true));
        static::assertTrue($options->isExportIDs());
        static::assertSame($options, $options->setExportFilesWithoutPrefix(true));
        static::assertTrue($options->isExportFilesWithoutPrefix());
        static::assertSame($options, $options->setExportFilesAsUUID(true));
        static::assertTrue($options->isExportFilesAsUUID());
    }
}
