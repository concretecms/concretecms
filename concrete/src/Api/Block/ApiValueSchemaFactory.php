<?php

namespace Concrete\Core\Api\Block;

use Concrete\Core\Api\ApiValueSchemaInterface;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Block\ExportDeclarations;
use Concrete\Core\Database\Connection\Connection;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types\Types;

/**
 * Build the JSON Schema describing the value accepted by a block type.
 *
 * Controllers implementing ApiValueSchemaInterface describe themselves; for the other ones the schema is
 * derived from the database table of the block and from its CIF export declarations. Such derived schemas
 * are marked with the "x-concrete-derived" keyword, since the save() method of a block controller may well
 * accept keys that aren't columns (and interpret columns in its own way).
 */
class ApiValueSchemaFactory
{
    /**
     * @var \Concrete\Core\Database\Connection\Connection
     */
    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Get the JSON Schema (as an array) describing the value accepted by a block type controller.
     *
     * @return array
     */
    public function getSchema(BlockController $controller): array
    {
        if ($controller instanceof ApiValueSchemaInterface) {
            return $controller->getApiValueSchema();
        }

        return $this->deriveSchema($controller);
    }

    /**
     * Derive the schema of a block type from its database table and its CIF export declarations.
     *
     * @return array
     */
    protected function deriveSchema(BlockController $controller): array
    {
        $declarations = $controller->getExportDeclarations();
        $schema = [
            'type' => 'object',
            'properties' => (object) [],
            // the save() method of the controller may accept other keys, and may interpret these ones its own way
            'x-concrete-derived' => true,
        ];
        $properties = [];
        foreach ($this->getTableColumns($declarations->getMainTable()) as $column) {
            $name = $column->getName();
            if (strcasecmp($name, 'bID') === 0) {
                continue;
            }
            $properties[$name] = $this->describeColumn($column, $declarations->getColumnReference($name));
        }
        if ($properties !== []) {
            $schema['properties'] = $properties;
        }
        $additionalTables = $declarations->getAdditionalTables();
        if ($additionalTables !== []) {
            // the API value only carries the first record of the main table (see BaseBlockTransformer)
            $schema['x-concrete-unrepresented-tables'] = $additionalTables;
        }

        return $schema;
    }

    /**
     * Get the columns of a database table.
     *
     * @return \Doctrine\DBAL\Schema\Column[]
     */
    protected function getTableColumns(string $table): array
    {
        if ($table === '') {
            return [];
        }
        $schemaManager = $this->connection->getSchemaManager();
        if (!$schemaManager->tablesExist([$table])) {
            return [];
        }

        return $schemaManager->listTableColumns($table);
    }

    /**
     * Describe a single database column.
     *
     * @param string|null $reference the kind of reference held by the column (if any)
     *
     * @return array
     */
    protected function describeColumn(Column $column, ?string $reference): array
    {
        // reading always gives strings, since the API exports the values out of the XML representation of
        // the block (see BaseBlockTransformer), and the columns holding a reference to another entity are
        // exported as a placeholder; writing accepts numbers as well, where the column holds numbers
        $types = ['string'];
        $numberType = $this->getNumberType($column);
        if ($numberType !== '') {
            $types[] = $numberType;
        } elseif ($length = $column->getLength()) {
            $maxLength = $length;
        }
        if (!$column->getNotnull()) {
            $types[] = 'null';
        }
        $result = ['type' => count($types) === 1 ? 'string' : $types];
        if (isset($maxLength)) {
            $result['maxLength'] = $maxLength;
        }
        $default = $column->getDefault();
        if ($default !== null) {
            $result['default'] = (string) $default;
        }
        if ($reference !== null) {
            $result['x-concrete-reference'] = $reference;
            $result['description'] = $this->getReferenceDescription($reference);
        }

        return $result;
    }

    /**
     * Describe how a column holding a reference to another entity is exchanged.
     */
    protected function getReferenceDescription(string $reference): string
    {
        switch ($reference) {
            case ExportDeclarations::REFERENCE_CONTENT:
                return 'Rich content: the references it contains are exported as placeholders, and resolved back when writing.'
                    . ' The images are <concrete-picture file-id="<file ID or file UUID>" /> elements (their other attributes are kept as they are),'
                    . ' whereas the links to files and to pages are {ccm:export:file::id=<file ID or file UUID>} and {ccm:export:page::id=<page ID>} placeholders.'
                ;
            case ExportDeclarations::REFERENCE_FILE:
                $placeholder = '{ccm:export:file::id=<file ID or file UUID>}';
                break;
            case ExportDeclarations::REFERENCE_PAGE:
                $placeholder = '{ccm:export:page::id=<page ID>}';
                break;
            case ExportDeclarations::REFERENCE_PAGE_TYPE:
                $placeholder = '{ccm:export:pagetype::id=<page type ID>}';
                break;
            case ExportDeclarations::REFERENCE_PAGE_FEED:
                $placeholder = '{ccm:export:pagefeed::id=<page feed ID>}';
                break;
            case ExportDeclarations::REFERENCE_FILE_FOLDER:
                $placeholder = '{ccm:export:filefolder::id=<file folder ID>}';
                break;
            default:
                return '';
        }

        return "Exported as a {$placeholder} placeholder (an empty reference is exported as 0); when writing, the placeholder is resolved, and the local ID is accepted too.";
    }

    /**
     * Get the JSON type of the numbers accepted by a database column.
     *
     * @return string an empty string if the column doesn't contain numbers
     */
    protected function getNumberType(Column $column): string
    {
        switch ($column->getType()->getName()) {
            // the boolean columns contain 0 and 1
            case Types::BOOLEAN:
            case Types::BIGINT:
            case Types::INTEGER:
            case Types::SMALLINT:
                return 'integer';
            case Types::DECIMAL:
            case Types::FLOAT:
                return 'number';
            default:
                return '';
        }
    }
}
