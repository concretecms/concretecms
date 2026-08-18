<?php

namespace Concrete\Tests\Block;

use Concrete\Core\Block\ExportDeclarations;
use Concrete\Tests\TestCase;

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

        $this->assertSame('btFaq', $declarations->getMainTable());
        $this->assertSame(['btFaq', 'btFaqEntries'], $declarations->getTables());
        $this->assertSame(['btFaqEntries'], $declarations->getAdditionalTables());
    }

    public function testTheMainTableIsAlwaysTheFirstOne(): void
    {
        // block types may or may not list the main table in $btExportTables
        $declarations = new ExportDeclarations('btMain', ['btOther'], []);

        $this->assertSame(['btMain', 'btOther'], $declarations->getTables());
        $this->assertSame(['btOther'], $declarations->getAdditionalTables());
    }

    public function testBlockTypesWithoutTables(): void
    {
        $declarations = new ExportDeclarations('', [], []);

        $this->assertSame('', $declarations->getMainTable());
        $this->assertSame([], $declarations->getTables());
        $this->assertSame([], $declarations->getAdditionalTables());
    }

    public function testColumns(): void
    {
        $declarations = $this->createDeclarations();

        $this->assertSame(['fID', 'fOnstateID'], $declarations->getColumns(ExportDeclarations::REFERENCE_FILE));
        $this->assertSame([], $declarations->getColumns(ExportDeclarations::REFERENCE_PAGE_FEED));
        // reference types without columns aren't listed
        $this->assertSame(
            [ExportDeclarations::REFERENCE_FILE, ExportDeclarations::REFERENCE_PAGE],
            $declarations->getReferenceTypes()
        );
    }

    public function testColumnReferencesAreLookedUpCaseInsensitively(): void
    {
        $declarations = $this->createDeclarations();

        $this->assertSame(ExportDeclarations::REFERENCE_FILE, $declarations->getColumnReference('fID'));
        $this->assertSame(ExportDeclarations::REFERENCE_FILE, $declarations->getColumnReference('fid'));
        $this->assertSame(ExportDeclarations::REFERENCE_PAGE, $declarations->getColumnReference('internalLinkCID'));
        $this->assertNull($declarations->getColumnReference('maxWidth'));
    }
}
