<?php

namespace Concrete\Tests\Api\Block;

use Concrete\Core\Api\ApiValueSchemaInterface;
use Concrete\Core\Api\Block\ApiValueSchemaFactory;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Block\ExportDeclarations;
use Concrete\Core\Entity\Block\BlockType\BlockType as BlockTypeEntity;
use Concrete\TestHelpers\Database\ConcreteDatabaseTestCase;

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
     * @return \Concrete\Core\Api\Block\ApiValueSchemaFactory
     */
    private function getFactory()
    {
        return app(ApiValueSchemaFactory::class);
    }

    public function testTheSchemaIsDerivedFromTheBlockTable(): void
    {
        $schema = $this->getFactory()->getSchema($this->getBlockTypeController('content'));

        $this->assertSame('object', $schema['type']);
        $this->assertTrue($schema['x-concrete-derived']);
        $this->assertArrayNotHasKey('bID', $schema['properties'], 'the block ID is not part of the value');
        $this->assertArrayHasKey('content', $schema['properties']);
        // the content block declares "content" in $btExportContentColumns
        $this->assertSame('content', $schema['properties']['content']['x-concrete-reference']);
    }

    public function testReferenceColumnsAreFlagged(): void
    {
        $schema = $this->getFactory()->getSchema($this->getBlockTypeController('image'));
        $properties = $schema['properties'];

        // an integer column, but what a client needs to know is that it holds a file reference
        $this->assertSame('integer', $properties['fID']['type']);
        $this->assertSame('file', $properties['fID']['x-concrete-reference']);
        $this->assertSame('page', $properties['internalLinkCID']['x-concrete-reference']);
        $this->assertSame('file', $properties['fileLinkID']['x-concrete-reference']);
        $this->assertArrayNotHasKey('x-concrete-reference', $properties['maxWidth']);
        $this->assertSame('string', $properties['altText']['type']);
    }

    public function testSecondaryTablesAreReported(): void
    {
        // the API value only carries the first record of the main table, so clients must know that
        // the block has data elsewhere
        $schema = $this->getFactory()->getSchema($this->getBlockTypeController('faq'));

        $this->assertSame(['btFaqEntries'], $schema['x-concrete-unrepresented-tables']);
    }

    public function testControllersCanDescribeThemselves(): void
    {
        $controller = new class() extends BlockController implements ApiValueSchemaInterface {
            protected $btTable = 'btContentLocal';

            public function getApiValueSchema(): array
            {
                return ['type' => 'object', 'properties' => ['whatever' => ['type' => 'string']]];
            }
        };

        $schema = $this->getFactory()->getSchema($controller);

        $this->assertSame(['whatever' => ['type' => 'string']], $schema['properties']);
        $this->assertArrayNotHasKey('x-concrete-derived', $schema);
    }

    public function testTheDeclarationsAreBuiltOnce(): void
    {
        $controller = $this->getBlockTypeController('content');

        $this->assertSame($controller->getExportDeclarations(), $controller->getExportDeclarations());
    }

    public function testDeclarationsCanBeCustomized(): void
    {
        $controller = new class() extends BlockController {
            protected $btTable = 'btContentLocal';

            protected function createExportDeclarations(): ExportDeclarations
            {
                return new ExportDeclarations('btContentLocal', [], [
                    ExportDeclarations::REFERENCE_FILE => ['content'],
                ]);
            }
        };

        $schema = $this->getFactory()->getSchema($controller);

        $this->assertSame('file', $schema['properties']['content']['x-concrete-reference']);
    }

    public function testBlockTypesWithoutATable(): void
    {
        $controller = new class() extends BlockController {
        };

        $this->assertEquals(
            ['type' => 'object', 'properties' => (object) [], 'x-concrete-derived' => true],
            $this->getFactory()->getSchema($controller)
        );
    }
}
