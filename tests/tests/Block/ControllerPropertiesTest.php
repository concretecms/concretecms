<?php

namespace Concrete\Tests\Block;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Utility\Service\Text;
use Concrete\Tests\TestCase;
use DoctrineXml\Parser;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Types;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionClass;

class ControllerPropertiesTest extends TestCase
{
    /**
     * @var \Doctrine\DBAL\Platforms\AbstractPlatform
     */
    private static $platform;

    /**
     * @var \Concrete\Core\Utility\Service\Text
     */
    private static $textService;

    public static function setUpBeforeClass(): void
    {
        self::$platform = app(Connection::class)->getDatabasePlatform();
        self::$textService = app(Text::class);
    }

    /**
     * Return the directory names of the blocks that have database tables.
     *
     * @return string[]
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

    #[DataProvider('provideBlocksWithDB')]
    public function testBlockControllerDefinesDBFields(string $dirname): void
    {
        $dbFile = FILENAME_BLOCK_DB;
        $controllerClass = 'Concrete\\Block\\' . self::$textService->camelcase($dirname) . '\\Controller';
        $tableName = self::getTableNameFromController($controllerClass);
        $this->assertNotEmpty($tableName, "The block in directory {$dirname} comes with a {$dbFile} file, but its controller getBlockTypeDatabaseTable() method returned an empty value");
        $table = self::getTableDefinition($dirname, $tableName);
        $this->assertNotNull($table, "The block controller in '{$dirname}' says it uses the {$tableName} table, but it's not present in the {$dbFile} file");
        $properties = self::getPropertiesFromController($controllerClass);
        foreach ($table->getColumns() as $column) {
            $property = $properties[$column->getName()] ?? null;
            $this->assertNotNull($property, "{$controllerClass} must define the '{$column->getName()}' property, accordingly to {$dbFile}.\nThe full list of properties should be:\n" . self::buildPHPProperties($table));
        }
    }

    private static function getTableNameFromController(string $controllerClassName): string
    {
        $instance = app($controllerClassName);
        $tableName = $instance->getBlockTypeDatabaseTable();

        return is_string($tableName) ? $tableName : '';
    }

    /**
     * @return \ReflectionProperty[] array keys are the property names
     */
    private static function getPropertiesFromController(string $controllerClassName): array
    {
        $class = new ReflectionClass($controllerClassName);
        $result = [];
        foreach ($class->getProperties() as $property) {
            $result[$property->getName()] = $property;
        }

        return $result;
    }

    private static function getTableDefinition(string $dirname, string $tableName): ?Table
    {
        $schema = Parser::fromFile(DIR_BASE_CORE . '/' . DIRNAME_BLOCKS . '/' . $dirname . '/' . FILENAME_BLOCK_DB, self::$platform);
        foreach ($schema->getTables() as $table) {
            if ($table->getName() === $tableName) {
                return $table;
            }
        }

        return null;
    }

    private static function buildPHPProperties(Table $table): string
    {
        $properties = [];

        foreach ($table->getColumns() as $column) {
            switch ($column->getName()) {
                case 'bID':
                    break;
                default:
                    $properties[] = self::buildPHPProperty($column);
                    break;
            }
        }

        return implode("\n\n", $properties);
    }

    private static function buildPHPProperty(Column $column): string
    {
        $indent = '    ';
        $phpDocType = '';
        switch ($column->getType()->getName()) {
            case Types::ARRAY:
                $phpDocType = 'array|null';
                break;
            case Types::BIGINT:
            case Types::INTEGER:
            case Types::SMALLINT:
                $phpDocType = 'int|string|null';
                break;
            case Types::DECIMAL:
            case Types::FLOAT:
                $phpDocType = 'float|string|null';
                break;
            case Types::BOOLEAN:
                $phpDocType = 'bool|int|string|null';
                break;
            case Types::ASCII_STRING:
            case Types::GUID:
            case Types::STRING:
            case Types::TEXT:
                $phpDocType = 'string|null';
                break;
        }
        $result = '';
        if ($phpDocType !== '') {
            $result = <<<EOT
{$indent}/**
{$indent} * @var {$phpDocType}
{$indent} */

EOT;
        }
        $result .= <<<EOT
{$indent}public \${$column->getName()};
EOT
        ;

        return $result;
    }
}
