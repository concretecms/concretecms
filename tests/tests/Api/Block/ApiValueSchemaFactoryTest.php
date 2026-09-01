<?php

declare(strict_types=1);

namespace Concrete\Tests\Api\Block;

use Concrete\Core\Api\ApiValueSchemaInterface;
use Concrete\Core\Api\Block\ApiValueSchemaFactory;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Block\ExportDeclarations;
use Concrete\Core\Entity\Block\BlockType\BlockType as BlockTypeEntity;
use Concrete\TestHelpers\Database\ConcreteDatabaseTestCase;

defined('C5_EXECUTE') or die('Access Denied.');

class ApiValueSchemaFactoryTest extends ConcreteDatabaseTestCase
{
    protected $tables = [
        'Blocks',
        'BlockTypeSets',
    ];

    protected $entityClassNames = [
        BlockTypeEntity::class,
    ];

    /**
     * @return \Concrete\Core\Block\BlockController
     */
    private function getBlockTypeController(string $handle)
    {
        if (BlockType::getByHandle($handle) === null) {
            BlockType::installBlockType($handle);
        }

        // fetch it again: installBlockType() doesn't load the controller
        return BlockType::getByHandle($handle)->getController();
    }

    /**
     * Get a controller whose schema the factory has to derive from the columns of the table of the image
     * block type, whatever the controller of that block type does with the API.
     *
     * @return \Concrete\Core\Block\BlockController
     */
    private function getDerivedImageController()
    {
        // the block type is installed for its table, which holds the columns the tests are about
        $this->getBlockTypeController('image');

        return new class extends BlockController {
            protected $btTable = 'btContentImage';

            protected $btExportFileColumns = ['fID', 'fOnstateID', 'fileLinkID'];

            protected $btExportPageColumns = ['internalLinkCID'];
        };
    }

    /**
     * @return \Concrete\Core\Api\Block\ApiValueSchemaFactory
     */
    private function getFactory()
    {
        return app(ApiValueSchemaFactory::class);
    }

    public function testTheSchemaIsDerivedFromTheBlockTable(): void
    {
        $schema = $this->getFactory()->getSchema($this->getBlockTypeController('content'));

        static::assertSame('object', $schema['type']);
        static::assertSame(['string', 'null'], $schema['properties']['content']['type']);
        static::assertTrue($schema['x-concrete-derived']);
        static::assertArrayNotHasKey('bID', $schema['properties'], 'the block ID is not part of the value');
        static::assertArrayHasKey('content', $schema['properties']);
        // the content block declares "content" in $btExportContentColumns
        static::assertSame('content', $schema['properties']['content']['x-concrete-reference']);
        $description = $schema['properties']['content']['description'];
        static::assertStringContainsString('<concrete-picture file-id="<file ID or file UUID>" />', $description);
        static::assertStringContainsString('{ccm:export:file:id=<file ID or file UUID>}', $description);
        static::assertStringContainsString('{ccm:export:page:id=<page ID>}', $description);
    }

    public function testReferenceColumnsAreFlagged(): void
    {
        $schema = $this->getFactory()->getSchema($this->getDerivedImageController());
        $properties = $schema['properties'];

        static::assertSame('file', $properties['fID']['x-concrete-reference']);
        static::assertSame('page', $properties['internalLinkCID']['x-concrete-reference']);
        static::assertSame('file', $properties['fileLinkID']['x-concrete-reference']);
        static::assertStringContainsString('{ccm:export:file:id=', $properties['fID']['description']);
        static::assertArrayNotHasKey('x-concrete-reference', $properties['maxWidth']);
        static::assertArrayNotHasKey('description', $properties['maxWidth']);
    }

    public function testTheFormatOfAReferenceIsAddedToItsDescription(): void
    {
        $schema = $this->getFactory()->describeReference(ExportDeclarations::REFERENCE_PAGE, [
            'type' => ['string', 'integer'],
            'description' => 'The page the block links to.',
        ]);

        // a block type describing itself says what the column holds, the factory how it's exchanged
        static::assertStringStartsWith("The page the block links to.\n\n", $schema['description']);
        static::assertStringContainsString('{ccm:export:page:id=<page ID>}', $schema['description']);
        static::assertSame('page', $schema['x-concrete-reference']);
        static::assertSame(['string', 'integer'], $schema['type']);
    }

    /**
     * The API exchanges every value of the record as a string, and the columns holding a reference are
     * exchanged as a placeholder: the schema must describe that, not the database column.
     */
    public function testTheAcceptedTypesAreDescribed(): void
    {
        $properties = $this->getFactory()->getSchema($this->getDerivedImageController())['properties'];

        // reading gives strings, writing accepts numbers too; the column can be emptied
        static::assertSame(['string', 'integer', 'null'], $properties['maxWidth']['type']);
        // a reference is a placeholder (a string) or the local ID
        static::assertSame(['string', 'integer', 'null'], $properties['fID']['type']);
        // the boolean columns of the blocks contain 0 and 1, and JSON has booleans
        static::assertSame(['boolean', 'string', 'integer'], $properties['openLinkInNewWindow']['type']);
        static::assertSame(['string', 'null'], $properties['altText']['type']);
        // the length is a constraint on what can be sent, unlike the type of the underlying column
        static::assertSame(255, $properties['altText']['maxLength']);
        static::assertArrayNotHasKey('maxLength', $properties['maxWidth']);
        static::assertArrayNotHasKey('nullable', $properties['maxWidth']);
        static::assertSame('0', $properties['maxWidth']['default']);
    }

    public function testControllersCanDescribeThemselves(): void
    {
        $controller = new class extends BlockController implements ApiValueSchemaInterface {
            protected $btTable = 'btContentLocal';

            public function getApiValueSchema(): array
            {
                return ['type' => 'object', 'properties' => ['whatever' => ['type' => 'string']]];
            }
        };

        $schema = $this->getFactory()->getSchema($controller);

        static::assertSame(['whatever' => ['type' => 'string']], $schema['properties']);
        static::assertArrayNotHasKey('x-concrete-derived', $schema);
    }

    public function testTheDeclarationsAreBuiltOnce(): void
    {
        $controller = $this->getBlockTypeController('content');

        static::assertSame($controller->getExportDeclarations(), $controller->getExportDeclarations());
    }

    public function testDeclarationsCanBeCustomized(): void
    {
        $controller = new class extends BlockController {
            protected $btTable = 'btContentLocal';

            protected function createExportDeclarations(): ExportDeclarations
            {
                return new ExportDeclarations('btContentLocal', [], [
                    ExportDeclarations::REFERENCE_FILE => ['content'],
                ]);
            }
        };

        $schema = $this->getFactory()->getSchema($controller);

        static::assertSame('file', $schema['properties']['content']['x-concrete-reference']);
    }

    public function testBlockTypesWithoutATable(): void
    {
        $controller = new class extends BlockController {
        };

        static::assertEquals(
            ['type' => 'object', 'properties' => (object) [], 'x-concrete-derived' => true],
            $this->getFactory()->getSchema($controller)
        );
    }
}
