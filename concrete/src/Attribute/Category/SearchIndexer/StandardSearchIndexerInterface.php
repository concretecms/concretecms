<?php

namespace Concrete\Core\Attribute\Category\SearchIndexer;

/**
 * Class to be implemented by attribute categories to define the attribute indexing table.
 */
interface StandardSearchIndexerInterface
{
    /**
     * Get the name of the indexing table (return false if there's no indexing table).
     *
     * @return string|false
     */
    public function getIndexedSearchTable();

    /**
     * Get the definition of the indexing table, excluding attribute-related fields (return false if there's no indexing table).
     * The resulting array can have these keys:
     * - columns: an array describing the table columns
     * - primary: an array containing the names of the columns that define the table primary key
     * - foreignKeys: an array describing the foreign keys.
     *
     * @return array|false
     *
     * @example Here's what the File attribute category returns:
     * [
     *     'columns' => [
     *         [
     *             'name' => 'fID',
     *             'type' => 'integer',
     *             'options' => ['unsigned' => true, 'default' => 0, 'notnull' => true],
     *         ],
     *     ],
     *     'primary' => ['fID'],
     *     'foreignKeys' => [
     *         [
     *             'foreignTable' => 'Files',
     *             'localColumns' => ['fID'],
     *             'foreignColumns' => ['fID'],
     *             'onUpdate' => 'CASCADE',
     *             'onDelete' => 'CASCADE',
     *         ],
     *     ],
     * ]
     */
    public function getSearchIndexFieldDefinition();

    /**
     * Get the value of the primary key column of the indexing table that identifies the object to be indexed.
     *
     * @param object $mixed The object for which we need the identifier
     *
     * @return mixed
     *
     * @example For the File attribute category, the file ID will be returned.
     */
    public function getIndexedSearchPrimaryKeyValue($mixed);
}
