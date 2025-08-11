<?php

declare(strict_types=1);

namespace Concrete\Tests\Utility\Service;

use Concrete\Core\Utility\Service\Xml;
use Concrete\Tests\TestCase;
use DateTimeImmutable;
use DateTimeInterface;
use SimpleXMLElement;

class XmlTest extends TestCase
{
    /**
     * @var \Concrete\Core\Utility\Service\Xml
     */
    protected static $xml;

    public static function setupBeforeClass(): void
    {
        self::$xml = app(Xml::class);
    }

    public function testAlias(): void
    {
        $instanceFromAlias = app('helper/xml');
        $instanceFromClassName = app(Xml::class);
        $this->assertSame($instanceFromClassName, $instanceFromAlias);
    }

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
    public function testGetBooleanValues(string $xml, bool $expected, string $childName = '', bool $isDefaultFallback = false): void
    {
        $element = $this->parseXml($xml);
        if ($childName !== '') {
            $element = $element->{$childName};
        }
        $actual = self::$xml->getBool($element);
        $this->assertSame($expected, $actual);
        if ($isDefaultFallback) {
            $actual = self::$xml->getBool($element, true);
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
    public function testGetBooleanAttributes(string $xml, string $attributeName, bool $expected, bool $isDefaultFallback = false): void
    {
        $element = $this->parseXml($xml);
        $actual = self::$xml->getBool($element[$attributeName]);
        $this->assertSame($expected, $actual);
        if ($isDefaultFallback) {
            $actual = self::$xml->getBool($element[$attributeName], true);
            $this->assertSame(true, $actual);
        }
    }

    public static function proviteCreateElementData(): array
    {
        $lf = "\n";
        $cr = "\r";
        $crlf = "\r\n";

        return [
            ['', ''],
            [null, ''],
            [DateTimeImmutable::createFromFormat(\DateTimeInterface::ISO8601, '2005-08-15T15:52:01+0000'), '2005-08-15 15:52:01'],
            [false, ''], // A better representation would be false (or at least 0), but we must preserve backward compatibility
            [true, '1'], // A better representation would be true, but we must preserve backward compatibility
            ['<', '<![CDATA[<]]>'],
            ['&', '<![CDATA[&]]>'],
            ['>', '<![CDATA[>]]>'],
            ['A <u>string</u> that contains ]]>', '<![CDATA[A <u>string</u> that contains ]]]]><![CDATA[>]]>'],
            ['<b>Test</b>!', '<![CDATA[<b>Test</b>!]]>'],
            ['"Hello" he said ', '"Hello" he said '],
            ["'Hello' she said ", "'Hello' she said "],
            ['First & Second', '<![CDATA[First & Second]]>'],
            ["First line{$lf}Second line", "First line{$lf}Second line"],
            ["<b>First</b> line{$lf}Second line", "<![CDATA[<b>First</b> line{$lf}Second line]]>"],
            // see https://www.w3.org/TR/2008/REC-xml-20081126/#sec-line-ends
            ["First line{$crlf}Second line", "First line{$lf}Second line"],
            ["First line{$crlf}Second line", "First line&#13;{$lf}Second line", Xml::FLAG_PRESERVE_CARRIAGE_RETURNS],
            ["First line{$cr}Second line", "First line{$lf}Second line"],
            ["First line{$cr}Second line", 'First line&#13;Second line', Xml::FLAG_PRESERVE_CARRIAGE_RETURNS],
            ["<b>First</b> line{$cr}Second line", "<![CDATA[<b>First</b> line{$lf}Second line]]>"],
            ["<b>First</b> line{$crlf}Second line", "<![CDATA[<b>First</b> line{$lf}Second line]]>"],
            ["<b>First</b> line{$crlf}Second line", "&lt;b&gt;First&lt;/b&gt; line&#13;{$lf}Second line", Xml::FLAG_PRESERVE_CARRIAGE_RETURNS],
            ["<b>First</b> line{$cr}Second line", "<![CDATA[<b>First</b> line{$lf}Second line]]>"],
            ["<b>First</b> line{$cr}Second line", '&lt;b&gt;First&lt;/b&gt; line&#13;Second line', Xml::FLAG_PRESERVE_CARRIAGE_RETURNS],
        ];
    }

    /**
     * @dataProvider proviteCreateElementData
     *
     * @param mixed $data
     */
    public function testCreateElement($data, string $expectedXml, int $flags = 0): void
    {
        $parent = $this->parseXml('<parent />');
        $child = self::$xml->createChildElement($parent, 'child', $data, $flags);
        $this->assertSame((string) $child, (string) $parent->child);
        $newParent = $this->parseXml($parent->asXML());
        $newChild = $newParent->child;
        $childOuterXML = $newChild->asXML();
        $childInnerXML = str_ends_with($childOuterXML, '/>') ? '' : preg_replace('_^<child>(.*)</child>$_s', '\\1', $childOuterXML);
        $this->assertSame($expectedXml, $childInnerXML);
        $expectedValue = $data instanceof DateTimeInterface ? $data->format('Y-m-d H:i:s') : (string) $data;
        if ($flags & Xml::FLAG_PRESERVE_CARRIAGE_RETURNS) {
            $this->assertSame($expectedValue, (string) $newChild);
        } else {
            $this->assertSame(strtr($expectedValue, ["\r\n" => "\n", "\r" => "\n"]), (string) $newChild);
        }
    }

    private function parseXml(string $xml): SimpleXMLElement
    {
        $element = simplexml_load_string($xml);
        $this->assertInstanceOf(SimpleXMLElement::class, $element, "Failed to process XML: {$xml}");

        return $element;
    }
}
