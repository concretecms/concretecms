<?php

declare(strict_types=1);

namespace Concrete\Core\Block;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * What a block type controller declares about the data it owns, so that it can be exported to (and
 * imported from) the installation-independent CIF format.
 *
 * @readonly
 *
 * @see \Concrete\Core\Block\BlockController::getExportDeclarations()
 */
final class ExportDeclarations
{
    /**
     * A column containing the ID of a page.
     *
     * @var string
     */
    public const REFERENCE_PAGE = 'page';

    /**
     * A column containing the ID of a file.
     *
     * @var string
     */
    public const REFERENCE_FILE = 'file';

    /**
     * A column containing the ID of a page type.
     *
     * @var string
     */
    public const REFERENCE_PAGE_TYPE = 'pagetype';

    /**
     * A column containing the ID of an RSS page feed.
     *
     * @var string
     */
    public const REFERENCE_PAGE_FEED = 'pagefeed';

    /**
     * A column containing the ID of a folder of files.
     *
     * @var string
     */
    public const REFERENCE_FILE_FOLDER = 'filefolder';

    /**
     * A column containing Rich Text or HTML.
     *
     * @var string
     */
    public const REFERENCE_CONTENT = 'content';

    /**
     * @readonly
     *
     * @var string
     */
    private $mainTable;

    /**
     * @readonly
     *
     * @var string[]
     */
    private $tables;

    /**
     * The declared columns, grouped by the kind of reference they contain.
     *
     * @readonly
     *
     * @var array<string,string[]>
     */
    private $referenceColumns;

    /**
     * The kind of reference of every declared column (keys are the lower-cased column names).
     *
     * @readonly
     *
     * @var array<string,string>
     */
    private $columnReferences;

    /**
     * @param string $mainTable the name of the main database table of the block type (an empty string if the block type has no table)
     * @param string[] $tables the names of all the database tables of the block type (it may be empty, and it may contain the main table)
     * @param array<string,string[]> $referenceColumns the declared columns, grouped by the kind of reference they contain (if a column is declared more than once, the first kind of reference wins)
     */
    public function __construct(string $mainTable, array $tables, array $referenceColumns)
    {
        $this->mainTable = $mainTable;
        $this->tables = $this->buildTables($mainTable, $tables);
        $this->referenceColumns = [];
        $this->columnReferences = [];
        foreach ($referenceColumns as $reference => $columns) {
            $columns = array_values(array_filter(array_map('strval', (array) $columns), static function ($column) {
                return $column !== '';
            }));
            if ($columns === []) {
                continue;
            }
            $this->referenceColumns[$reference] = $columns;
            foreach ($columns as $column) {
                $key = strtolower($column);
                if (!isset($this->columnReferences[$key])) {
                    // a column declared more than once keeps the kind of reference declared first
                    $this->columnReferences[$key] = (string) $reference;
                }
            }
        }
    }

    /**
     * Get the name of the main database table of the block type (an empty string if the block type has no table).
     */
    public function getMainTable(): string
    {
        return $this->mainTable;
    }

    /**
     * Get the names of all the database tables of the block type, the main one being the first.
     *
     * @return string[]
     */
    public function getTables(): array
    {
        return $this->tables;
    }

    /**
     * Get the names of the database tables of the block type, excluding the main one.
     *
     * @return string[]
     */
    public function getAdditionalTables(): array
    {
        return array_values(array_slice($this->tables, 1));
    }

    /**
     * Get the kinds of reference for which at least one column has been declared.
     *
     * @return string[]
     */
    public function getReferenceTypes(): array
    {
        return array_keys($this->referenceColumns);
    }

    /**
     * Get the names of the columns containing a specific kind of reference.
     *
     * @param string $referenceType one of the ExportDeclarations::REFERENCE_... constants
     *
     * @return string[]
     */
    public function getColumns(string $referenceType): array
    {
        return $this->referenceColumns[$referenceType] ?? [];
    }

    /**
     * Get the kind of reference contained in a column.
     *
     * @return string|null one of the ExportDeclarations::REFERENCE_... constants, NULL if the column contains no reference
     */
    public function getColumnReference(string $columnName): ?string
    {
        return $this->columnReferences[strtolower($columnName)] ?? null;
    }

    /**
     * @param string[] $tables
     *
     * @return string[]
     */
    private function buildTables(string $mainTable, array $tables): array
    {
        $result = $mainTable === '' ? [] : [$mainTable];
        foreach ($tables as $table) {
            $table = (string) $table;
            if ($table !== '' && !in_array($table, $result, true)) {
                $result[] = $table;
            }
        }

        return $result;
    }
}
