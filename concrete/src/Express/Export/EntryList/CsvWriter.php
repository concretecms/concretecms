<?php

namespace Concrete\Core\Express\Export\EntryList;

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\EntryList;
use League\Csv\Writer;

/**
 * A Writer class for Express Entry objects
 */
class CsvWriter
{

    /** @var Writer The writer we use to output */
    protected $writer;

    public function __construct(Writer $writer)
    {
        $this->writer = $writer;
    }

    public function insertHeaders(Entity $entity)
    {
        $this->writer->insertOne(iterator_to_array($this->getHeaders($entity)));
    }

    /**
     * Insert all data from the passed EntryList
     * @param \Concrete\Core\Express\EntryList $list
     */
    public function insertEntryList(EntryList $list)
    {
        $list = clone $list;
        $this->writer->insertAll($this->projectList($list));
    }

    /**
     * A generator that takes an EntryList and converts it to CSV rows
     * @param \Concrete\Core\Express\EntryList $list
     * @return \Generator
     */
    private function projectList(EntryList $list)
    {
        $statement = $list->deliverQueryObject()->execute();

        foreach ($statement as $result) {
            if ($entry = $list->getResult($result)) {
                yield iterator_to_array($this->projectEntry($entry));
            }
        }
    }

    /**
     * Turn an Entry into an array
     * @param \Concrete\Core\Entity\Express\Entry $entry
     * @return array
     */
    private function projectEntry(Entry $entry)
    {
        $attributes = $entry->getAttributes();
        foreach ($attributes as $attribute) {
            yield $attribute->getPlainTextValue();
        }
    }

    /**
     * A generator that returns all headers
     * @param \Concrete\Core\Entity\Express\Entity $entity
     * @return \Generator
     */
    private function getHeaders(Entity $entity)
    {
        $attributes = $entity->getAttributes();
        foreach ($attributes as $attribute) {
            yield $attribute->getAttributeKeyDisplayName();
        }
    }

}
