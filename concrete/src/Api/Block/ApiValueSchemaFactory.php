<?php

namespace Concrete\Core\Api\Block;

use Concrete\Core\Api\ApiValueSchemaInterface;
use Concrete\Core\Block\BlockController;
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
        $result = ['type' => $this->getJsonType($column)];
        $length = $column->getLength();
        if ($result['type'] === 'string' && $length) {
            $result['maxLength'] = $length;
        }
        if (!$column->getNotnull()) {
            $result['nullable'] = true;
        }
        $default = $column->getDefault();
        if ($default !== null) {
            $result['default'] = $default;
        }
        if ($reference !== null) {
            // the value may also be a {ccm:export:...} placeholder (a <concrete-picture> element for "content")
            $result['x-concrete-reference'] = $reference;
        }

        return $result;
    }

    /**
     * Get the JSON type corresponding to the type of a database column.
     */
    protected function getJsonType(Column $column): string
    {
        switch ($column->getType()->getName()) {
            case Types::BOOLEAN:
                return 'boolean';
            case Types::BIGINT:
            case Types::INTEGER:
            case Types::SMALLINT:
                return 'integer';
            case Types::DECIMAL:
            case Types::FLOAT:
                return 'number';
            default:
                return 'string';
        }
    }
}
