<?php
namespace Concrete\Core\Csv\Strategy;

use Concrete\Core\Express\EntryList;

/**
 * Class EntryListStrategy.
 *
 * Turns entries from an EntryList into rows for a CSV
 */
class EntryListStrategy implements StrategyInterface
{
    private $rows = [];
    private $entries;
    private $entity;

    /**
     * EntryListStrategy constructor.
     *
     * @param EntryList $entryList
     */
    public function __construct(EntryList $entryList)
    {
        $this->entries = $entryList;
        $this->entity = $entryList->getEntity();
    }

    public function getRows()
    {
        $this->processRows();

        return $this->rows;
    }

    private function processRows()
    {
        $this->processHeaders();
        $this->processEntries();
    }

    private function processEntries()
    {
        $entries = $this->entries->getResults();
        foreach ($entries as $entry) {
            $row = [];
            foreach ($entry->getAttributes() as $attribute) {
                $row[] = $attribute->getPlainTextValue();
            }
            $this->rows[] = $row;
        }
    }

    private function processHeaders()
    {
        $headers = [];
        foreach ($this->entity->getAttributes() as $attribute) {
            $headers[] = $attribute->getAttributeKeyName();
        }

        $this->rows = array_merge([$headers], $this->rows);
    }
}
