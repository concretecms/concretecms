<?php

namespace Concrete\Tests\Api\Fractal\Transformer;

use Concrete\Core\Api\Fractal\Transformer\BaseBlockTransformer;
use Concrete\Tests\TestCase;
use SimpleXMLElement;

class BaseBlockTransformerTest extends TestCase
{
    /**
     * @return \Concrete\Core\Api\Fractal\Transformer\BaseBlockTransformer
     */
    private function createTransformer()
    {
        return new class() extends BaseBlockTransformer {
            public function extract(SimpleXMLElement $exportNode): array
            {
                return $this->extractBlockValue($exportNode);
            }
        };
    }

    public function testValuesAreExtractedFromTheFirstRecord(): void
    {
        $exportNode = new SimpleXMLElement(<<<'EOT'
<temporary-element>
    <data table="btContentLocal">
        <record>
            <content>Hello</content>
            <displayOrder>0</displayOrder>
        </record>
    </data>
</temporary-element>
EOT
        );

        $this->assertSame(
            ['content' => 'Hello', 'displayOrder' => '0'],
            $this->createTransformer()->extract($exportNode)
        );
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
        $exportNode = new SimpleXMLElement(<<<'EOT'
<temporary-element>
    <data table="btSomeTable">
        <record>
            <nullValue null="true"></nullValue>
            <emptyValue></emptyValue>
            <notNullValue null="false"></notNullValue>
            <filledValue>0</filledValue>
        </record>
    </data>
</temporary-element>
EOT
        );

        $this->assertSame(
            [
                'nullValue' => null,
                'emptyValue' => '',
                'notNullValue' => '',
                'filledValue' => '0',
            ],
            $this->createTransformer()->extract($exportNode)
        );
    }

    public function testNoRecord(): void
    {
        $exportNode = new SimpleXMLElement('<temporary-element><data table="btSomeTable"></data></temporary-element>');

        $this->assertSame([], $this->createTransformer()->extract($exportNode));
    }
}
