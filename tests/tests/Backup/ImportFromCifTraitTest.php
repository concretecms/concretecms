<?php

declare(strict_types=1);

namespace Concrete\Tests\Backup;

use Concrete\Core\Backup\ContentImporter\ImportFromCifTrait;
use Concrete\Tests\TestCase;
use SimpleXMLElement;

class ImportFromCifTraitTest extends TestCase
{
    use ImportFromCifTrait;

    public static function provideBooleanValuesCases(): array
    {
        return [
            ['<foo />', false],
            ['<foo>false</foo>', false],
            ['<foo>no</foo>', false],
            ['<foo>off</foo>', false],
            ['<foo>0</foo>', false],
            ['<foo>true</foo>', true],
            ['<foo>yes</foo>', true],
            ['<foo>on</foo>', true],
            ['<foo>1</foo>', true],
            ['<foo> false </foo>', false],
            ['<foo> no </foo>', false],
            ['<foo> off </foo>', false],
            ['<foo> 0 </foo>', false],
            ['<foo> true </foo>', true],
            ['<foo> yes </foo>', true],
            ['<foo> on </foo>', true],
            ['<foo> 1 </foo>', true],
            ['<foo>unrecognized</foo>', false, '', true],
            ['<foo><bar>false</bar><baz>true</baz></foo>', false],
            ['<foo><bar>false</bar><baz>true</baz></foo>', false, 'bar'],
            ['<foo><bar>false</bar><baz>true</baz></foo>', true, 'baz'],
            ['<foo><bar>false</bar><baz>true</baz></foo>', false, 'qux', true],
        ];
    }

    /**
     * @dataProvider provideBooleanValuesCases
     */
    public function testBooleanValues(string $xml, bool $expected, string $childName = '', bool $isDefaultFallback = false): void
    {
        $element = $this->parseXml($xml);
        if ($childName !== '') {
            $element = $element->{$childName};
        }
        $actual = self::getBoolFromCif($element);
        $this->assertSame($expected, $actual);
        if ($isDefaultFallback) {
            $actual = self::getBoolFromCif($element, true);
            $this->assertSame(true, $actual);
        }
    }

    public static function provideBooleanAttributeCases(): array
    {
        return [
            ['<foo />', 'bar', false, true],
            ['<foo bar="" />', 'bar', false],
            ['<foo bar="false" />', 'bar', false],
            ['<foo bar="no" />', 'bar', false],
            ['<foo bar="off" />', 'bar', false],
            ['<foo bar="0" />', 'bar', false],
            ['<foo bar="true" />', 'bar', true],
            ['<foo bar="yes" />', 'bar', true],
            ['<foo bar="on" />', 'bar', true],
            ['<foo bar="1" />', 'bar', true],
            ['<foo bar="" />', 'baz', false, true],
            ['<foo bar="false" />', 'baz', false, true],
            ['<foo bar="no" />', 'baz', false, true],
            ['<foo bar="off" />', 'baz', false, true],
            ['<foo bar="0" />', 'baz', false, true],
            ['<foo bar="true" />', 'baz', false, true],
            ['<foo bar="yes" />', 'baz', false, true],
            ['<foo bar="on" />', 'baz', false, true],
            ['<foo bar="1" />', 'baz', false, true],
        ];
    }

    /**
     * @dataProvider provideBooleanAttributeCases
     */
    public function testBooleanAttributes(string $xml, string $attributeName, bool $expected, bool $isDefaultFallback = false): void
    {
        $element = $this->parseXml($xml);
        $actual = self::getBoolFromCif($element[$attributeName]);
        $this->assertSame($expected, $actual);
        if ($isDefaultFallback) {
            $actual = self::getBoolFromCif($element[$attributeName], true);
            $this->assertSame(true, $actual);
        }
    }

    private function parseXml(string $xml): SimpleXMLElement
    {
        $element = simplexml_load_string($xml);
        $this->assertInstanceOf(\SimpleXMLElement::class, $element, "Failed to process XML: [$xml}");

        return $element;
    }
}
