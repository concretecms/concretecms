<?php

declare(strict_types=1);

namespace Concrete\Core\Api\Block;

use Concrete\Core\Api\ApiValueSchemaInterface;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Block\ExportDeclarations;
use Concrete\Core\Database\Connection\Connection;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types\Types;

defined('C5_EXECUTE') or die('Access Denied.');

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
     * @param string $reference the kind of reference held by the column (an empty string if it holds no reference)
     */
    protected function describeColumn(Column $column, string $reference): array
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

        return $this->describeReference($reference, $result);
    }

    /**
     * Describe a value holding a reference to another entity.
     *
     * @param string $reference the kind of reference held by the value (one of the ExportDeclarations::REFERENCE_... constants, or an empty string if it holds no reference)
     * @param array<string,mixed> $schema the already known schema of the value
     *
     * @return array<string,mixed>
     */
    public function describeReference(string $reference, array $schema = ['type' => 'string']): array
    {
        if ($reference === '') {
            return $schema;
        }
        $schema += ['x-concrete-reference' => $reference];
        $referenceDescription = $this->getReferenceDescription($reference);
        if ($referenceDescription !== '') {
            // whoever describes the column says what it holds: let's add the format of its value
            $description = rtrim((string) ($schema['description'] ?? ''));
            // the descriptions are CommonMark: a blank line makes them two paragraphs
            $schema['description'] = $description === '' ? $referenceDescription : "{$description}\n\n{$referenceDescription}";
        }

        return $schema;
    }

    /**
     * Describe how a value holding a reference to another entity is exchanged.
     */
    protected function getReferenceDescription(string $reference): string
    {
        switch ($reference) {
            case ExportDeclarations::REFERENCE_CONTENT:
                return 'Rich content: the references it contains are exchanged as placeholders, and resolved back when writing.'
                    . ' The images are <concrete-picture file-id="<file ID or file UUID>" /> elements (their other attributes are kept as they are),'
                    . ' whereas the links to files and to pages are {ccm:export:file:id=<file ID or file UUID>} and {ccm:export:page:id=<page ID>} placeholders.';
            case ExportDeclarations::REFERENCE_FILE:
                return 'Exchanged as the UUID of the file, or as its ID when it has no UUID (0 when it refers to no file);'
                    . ' when writing, both of them are accepted, as well as a {ccm:export:file:id=<file ID or file UUID>} placeholder.';
            case ExportDeclarations::REFERENCE_PAGE:
                $subject = 'page';
                break;
            case ExportDeclarations::REFERENCE_PAGE_TYPE:
                $subject = 'page type';
                break;
            case ExportDeclarations::REFERENCE_PAGE_FEED:
                $subject = 'page feed';
                break;
            case ExportDeclarations::REFERENCE_FILE_FOLDER:
                $subject = 'file folder';
                break;
            default:
                return '';
        }
        $placeholder = '{ccm:export:' . str_replace(' ', '', $subject) . ':id=<' . $subject . ' ID>}';

        return "Exchanged as the ID of the {$subject} (0 when it refers to no {$subject}); when writing, a {$placeholder} placeholder is accepted too.";
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
