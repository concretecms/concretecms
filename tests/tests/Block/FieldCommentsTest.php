<?php

declare(strict_types=1);

namespace Concrete\Tests\Block;

use Concrete\Tests\TestCase;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Every field of every table of a block type must say what it holds: that's where the APIs take the
 * description of the fields of a block from.
 */
class FieldCommentsTest extends TestCase
{
    /**
     * The longest comment that the concrete-cif schema accepts for a field.
     *
     * @var int
     */
    private const MAX_LENGTH = 255;

    /**
     * The namespace of the documents that declare the tables of the block types.
     *
     * @var string
     */
    private const DOCTRINE_XML_NAMESPACE = 'http://www.concrete5.org/doctrine-xml/0.5';

    /**
     * Return the directory names of the blocks that have database tables.
     *
     * @return array<array{0: string}>
     */
    public static function provideBlocksWithDB(): array
    {
        $result = [];
        foreach (scandir(DIR_BASE_CORE . '/' . DIRNAME_BLOCKS) as $item) {
            if (is_file(DIR_BASE_CORE . '/' . DIRNAME_BLOCKS . '/' . $item . '/' . FILENAME_BLOCK_DB)) {
                $result[] = [$item];
            }
        }

        return $result;
    }

    /**
     * @dataProvider provideBlocksWithDB
     */
    public function testEveryFieldIsDescribed(string $dirname): void
    {
        $undescribed = [];
        foreach ($this->getFields($dirname) as $name => $field) {
            // bID is the block the row belongs to: the same field in every table of every block type,
            // which no API hands over
            if ((string) $field['name'] === 'bID') {
                continue;
            }
            if (trim((string) $field['comment']) === '') {
                $undescribed[] = $name;
            }
        }

        static::assertSame([], $undescribed, "These fields of the {$dirname} block have no comment saying what they hold:\n- " . implode("\n- ", $undescribed));
    }

    /**
     * @dataProvider provideBlocksWithDB
     */
    public function testNoDescriptionIsTooLong(string $dirname): void
    {
        $tooLong = [];
        foreach ($this->getFields($dirname) as $name => $field) {
            $length = mb_strlen(trim((string) $field['comment']));
            if ($length > self::MAX_LENGTH) {
                $tooLong[] = "{$name} ({$length} characters)";
            }
        }

        static::assertSame([], $tooLong, "The comment of these fields of the {$dirname} block is longer than " . self::MAX_LENGTH . " characters, which the concrete-cif schema doesn't accept:\n- " . implode("\n- ", $tooLong));
    }

    /**
     * Get the fields of every table of a block, by their qualified name.
     *
     * @return array<string,\SimpleXMLElement>
     */
    private function getFields(string $dirname): array
    {
        $file = DIR_BASE_CORE . '/' . DIRNAME_BLOCKS . '/' . $dirname . '/' . FILENAME_BLOCK_DB;
        $xml = simplexml_load_file($file);
        static::assertInstanceOf(\SimpleXMLElement::class, $xml, "Failed to parse {$file}");
        // the fields are looked for where a doctrine-xml document keeps them: finding none means that
        // the file is something else, and that this test would pass by looking at nothing
        $xml->registerXPathNamespace('dx', self::DOCTRINE_XML_NAMESPACE);
        static::assertNotEmpty($xml->xpath('/dx:schema/dx:table/dx:field'), "{$file} is not a doctrine-xml document, or it declares no field");
        $fields = [];
        foreach ($xml->table as $table) {
            foreach ($table->field as $field) {
                $fields["{$table['name']}.{$field['name']}"] = $field;
            }
        }

        return $fields;
    }
}
