<?php

declare(strict_types=1);

namespace Concrete\Core\Block\Traits;

use League\Fractal\Resource\Item;
use League\Fractal\Resource\ResourceInterface;
use League\Fractal\TransformerAbstract;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Helps the block type controllers that build their own API value instead of letting the API derive it.
 *
 * The API builds the value of a block out of the first record of the main table of its CIF export (see
 * BaseBlockTransformer), which is not enough for the blocks storing their data elsewhere, or wanting to
 * publish it in a friendlier shape. Those controllers implement ApiResourceValueInterface, and this trait
 * provides them with the plumbing:
 * - getApiValueResource() is implemented once and for all
 * - serializeValueForApi() returns by default the very same value the API would derive: override it to add
 *   or reshape what's needed, with the help of serializeTablesForApi() and serializeRecordForApi()
 * - deserializeRecordsFromApi() reads back a list of records written that way
 */
trait CustomApiValueTrait
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Api\ApiResourceValueInterface::getApiValueResource()
     */
    public function getApiValueResource(): ?ResourceInterface
    {
        // the value is entirely built by serializeValueForApi(): the transformer is just a pass-through
        $transformer = new class() extends TransformerAbstract {
            /**
             * @param array<string,mixed> $value
             *
             * @return array<string,mixed>
             */
            public function transform(array $value): array
            {
                return $value;
            }
        };

        return new Item($this->serializeValueForApi(), $transformer);
    }

    /**
     * Get the value of this block as it's exposed by the API.
     *
     * By default it's the first record of the main table, that is what the API would expose anyway.
     *
     * @return array<string,mixed>
     */
    protected function serializeValueForApi(): array
    {
        $records = $this->serializeTablesForApi();
        $mainTable = (string) $this->getBlockTypeDatabaseTable();

        return $records[$mainTable][0] ?? [];
    }

    /**
     * Serialize for the API all the database tables of this block, starting from its CIF representation, so
     * that the exchanged values are the same ones found in a CIF file (references are placeholders, NULL
     * values are distinct from empty strings).
     *
     * @return array<string,array[]> the keys are the names of the database tables of the block, the values
     *                               are the list of their records
     */
    protected function serializeTablesForApi(): array
    {
        $blockNode = new \SimpleXMLElement('<block></block>');
        $this->export($blockNode);
        $result = [];
        foreach ($blockNode->data as $data) {
            $table = (string) $data['table'];
            $result[$table] = [];
            foreach ($data->record as $record) {
                $result[$table][] = $this->serializeRecordForApi($table, $record);
            }
        }

        return $result;
    }

    /**
     * Serialize for the API a single <record> element of the CIF representation of this block.
     *
     * @param string $table the name of the database table the record belongs to
     *
     * @return array<string,string|null>
     */
    protected function serializeRecordForApi(string $table, \SimpleXMLElement $record): array
    {
        $result = [];
        foreach ($record->children() as $child) {
            $value = (string) $child;
            if ($value === '' && isset($child['null']) && filter_var((string) $child['null'], FILTER_VALIDATE_BOOLEAN)) {
                // that's how the export() method marks NULL values
                $value = null;
            }
            $result[$child->getName()] = $value;
        }

        return $result;
    }

    /**
     * Deserialize the list of records held by one of the keys of a value received via the API, resolving the
     * placeholders they contain.
     *
     * If the value doesn't contain the key at all, the records currently exposed under that key are
     * returned: that's how a partial update doesn't wipe out what it doesn't mention.
     *
     * @param array<string,mixed> $value the received value: the key is removed from it
     * @param string $key the key of the value holding the records
     *
     * @return array[]
     */
    protected function deserializeRecordsFromApi(array &$value, string $key): array
    {
        if (array_key_exists($key, $value)) {
            $records = is_array($value[$key]) ? $value[$key] : [];
            unset($value[$key]);
        } elseif ($this->bID) {
            $apiValue = $this->serializeValueForApi();
            $records = isset($apiValue[$key]) && is_array($apiValue[$key]) ? $apiValue[$key] : [];
        } else {
            $records = [];
        }
        $declarations = $this->getExportDeclarations();
        $result = [];
        foreach ($records as $record) {
            if (!is_array($record)) {
                continue;
            }
            $importedRecord = [];
            foreach ($record as $column => $columnValue) {
                if (is_string($columnValue) || is_int($columnValue) || is_float($columnValue)) {
                    $importedRecord[$column] = $this->importReferenceValue((string) $columnValue, $declarations->getColumnReference((string) $column));
                } else {
                    $importedRecord[$column] = $columnValue;
                }
            }
            $result[] = $importedRecord;
        }

        return $result;
    }
}
