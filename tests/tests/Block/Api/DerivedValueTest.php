<?php

declare(strict_types=1);

namespace Concrete\Tests\Block\Api;

use Concrete\Core\Api\ApiResourceValueInterface;
use Concrete\Core\Block\BlockController;
use Concrete\Tests\TestCase;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\ResourceInterface;
use League\Fractal\TransformerAbstract;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Tests the value that the API derives for the block types that don't build it themselves: it's the first
 * record of the main table of their CIF representation.
 *
 * @see \Concrete\Core\Block\BlockController::getApiValue()
 */
class DerivedValueTest extends TestCase
{
    public function testValuesAreExtractedFromTheFirstRecord(): void
    {
        $controller = $this->createControllerExporting(
            <<<'EOT'
            <block>
                <data table="btContentLocal">
                    <record>
                        <content>Hello</content>
                        <displayOrder>0</displayOrder>
                    </record>
                </data>
            </block>
            EOT
        );

        static::assertSame(['content' => 'Hello', 'displayOrder' => '0'], $controller->getApiValue());
    }

    /**
     * The block controller export() method marks NULL values with a null="true" attribute:
     * without honoring it, NULL values would become empty strings, and getImportData()
     * wouldn't be able to tell them apart when the value is imported back.
     *
     * @see \Concrete\Core\Block\BlockController::export()
     * @see \Concrete\Core\Block\BlockController::getImportData()
     */
    public function testNullValuesAreKeptDistinctFromEmptyStrings(): void
    {
        $controller = $this->createControllerExporting(
            <<<'EOT'
            <block>
                <data table="btSomeTable">
                    <record>
                        <nullValue null="true"></nullValue>
                        <emptyValue></emptyValue>
                        <notNullValue null="false"></notNullValue>
                        <filledValue>0</filledValue>
                    </record>
                </data>
            </block>
            EOT
        );

        static::assertSame(
            [
                'nullValue' => null,
                'emptyValue' => '',
                'notNullValue' => '',
                'filledValue' => '0',
            ],
            $controller->getApiValue()
        );
    }

    public function testNoRecord(): void
    {
        $controller = $this->createControllerExporting('<block><data table="btSomeTable"></data></block>');

        static::assertSame([], $controller->getApiValue());
    }

    public function testControllersCanBuildTheValueThemselves(): void
    {
        $controller = new class extends BlockController implements ApiResourceValueInterface {
            public function getApiValueResource(): ?ResourceInterface
            {
                $transformer = new class extends TransformerAbstract {
                    public function transform(array $value): array
                    {
                        return $value;
                    }
                };

                return new Item(['whatever' => ['it', 'wants']], $transformer);
            }
        };

        static::assertSame(['whatever' => ['it', 'wants']], $controller->getApiValue());
    }

    /**
     * Get a block controller whose CIF representation is the given XML.
     *
     * @return \Concrete\Core\Block\BlockController
     */
    private function createControllerExporting(string $xml)
    {
        $controller = new class extends BlockController {
            /**
             * @var string
             */
            public $exportedXml = '';

            public function export(\SimpleXMLElement $blockNode)
            {
                $target = dom_import_simplexml($blockNode);
                foreach ((new \SimpleXMLElement($this->exportedXml))->children() as $child) {
                    $target->appendChild($target->ownerDocument->importNode(dom_import_simplexml($child), true));
                }
            }
        };
        $controller->exportedXml = $xml;

        return $controller;
    }
}
