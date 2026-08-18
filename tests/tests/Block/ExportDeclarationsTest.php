<?php

declare(strict_types=1);

namespace Concrete\Tests\Block;

use Concrete\Core\Block\ExportDeclarations;
use Concrete\Tests\TestCase;

defined('C5_EXECUTE') or die('Access Denied.');

class ExportDeclarationsTest extends TestCase
{
    /**
     * @return \Concrete\Core\Block\ExportDeclarations
     */
    private function createDeclarations()
    {
        return new ExportDeclarations(
            'btFaq',
            ['btFaq', 'btFaqEntries'],
            [
                ExportDeclarations::REFERENCE_FILE => ['fID', 'fOnstateID'],
                ExportDeclarations::REFERENCE_PAGE => ['internalLinkCID'],
                ExportDeclarations::REFERENCE_CONTENT => [],
            ]
        );
    }

    public function testTables(): void
    {
        $declarations = $this->createDeclarations();

        static::assertSame('btFaq', $declarations->getMainTable());
        static::assertSame(['btFaq', 'btFaqEntries'], $declarations->getTables());
        static::assertSame(['btFaqEntries'], $declarations->getAdditionalTables());
    }

    public function testTheMainTableIsAlwaysTheFirstOne(): void
    {
        // block types may or may not list the main table in $btExportTables
        $declarations = new ExportDeclarations('btMain', ['btOther'], []);

        static::assertSame(['btMain', 'btOther'], $declarations->getTables());
        static::assertSame(['btOther'], $declarations->getAdditionalTables());
    }

    public function testBlockTypesWithoutTables(): void
    {
        $declarations = new ExportDeclarations('', [], []);

        static::assertSame('', $declarations->getMainTable());
        static::assertSame([], $declarations->getTables());
        static::assertSame([], $declarations->getAdditionalTables());
    }

    public function testColumns(): void
    {
        $declarations = $this->createDeclarations();

        static::assertSame(['fID', 'fOnstateID'], $declarations->getColumns(ExportDeclarations::REFERENCE_FILE));
        static::assertSame([], $declarations->getColumns(ExportDeclarations::REFERENCE_PAGE_FEED));
        // reference types without columns aren't listed
        static::assertSame(
            [ExportDeclarations::REFERENCE_FILE, ExportDeclarations::REFERENCE_PAGE],
            $declarations->getReferenceTypes()
        );
    }

    public function testColumnReferencesAreLookedUpCaseInsensitively(): void
    {
        $declarations = $this->createDeclarations();

        static::assertSame(ExportDeclarations::REFERENCE_FILE, $declarations->getColumnReference('fID'));
        static::assertSame(ExportDeclarations::REFERENCE_FILE, $declarations->getColumnReference('fid'));
        static::assertSame(ExportDeclarations::REFERENCE_PAGE, $declarations->getColumnReference('internalLinkCID'));
        static::assertNull($declarations->getColumnReference('maxWidth'));
    }
}
