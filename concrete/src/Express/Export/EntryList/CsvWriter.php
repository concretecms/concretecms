<?php

namespace Concrete\Core\Express\Export\EntryList;

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\EntryList;
use Concrete\Core\Localization\Service\Date;
use League\Csv\Writer;

/**
 * A Writer class for Express Entry objects
 * @since 8.2.0
 */
class CsvWriter
{

    /** @var Writer The writer we use to output */
    protected $writer;

    /**
     * @var Date
     */
    protected $dateFormatter;

    public function __construct(Writer $writer, Date $dateFormatter)
    {
        $this->writer = $writer;
        $this->dateFormatter = $dateFormatter;
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
        $headers = array_keys(iterator_to_array($this->getHeaders($list->getEntity())));
        $statement = $list->deliverQueryObject()->execute();

        foreach ($statement as $result) {
            if ($entry = $list->getResult($result)) {
                yield $this->orderedEntry(iterator_to_array($this->projectEntry($entry)), $headers);
            }
        }
    }

    /**
     * Return an entry in proper order
     * @param array $entry
     * @param array $headerKeys
     *
     * @return array
     */
    private function orderedEntry(array $entry, array $headerKeys)
    {
        $result = [];

        foreach ($headerKeys as $key) {
            $result[$key] = $entry[$key];
        }

        return $result;
    }

    /**
     * Turn an Entry into an array
     * @param \Concrete\Core\Entity\Express\Entry $entry
     * @return array
     */
    private function projectEntry(Entry $entry)
    {
        $date = $entry->getDateCreated();
        if ($date) {
            yield 'ccm_date_created' => $this->dateFormatter->formatCustom(\DateTime::ATOM, $date);
        } else {
            yield 'ccm_date_created' => null;
        }

        $author = $entry->getAuthor();
        if ($author) {
            yield 'author_name' => $author->getUserInfoObject()->getUserDisplayName();
        } else {
            yield 'author_name' => null;
        }

        $attributes = $entry->getAttributes();
        foreach ($attributes as $attribute) {
            yield $attribute->getAttributeKey()->getAttributeKeyHandle() => $attribute->getPlainTextValue();
        }
    }

    /**
     * A generator that returns all headers
     * @param \Concrete\Core\Entity\Express\Entity $entity
     * @return \Generator
     */
    private function getHeaders(Entity $entity)
    {
        yield 'ccm_date_created' => 'dateCreated';
        yield 'author_name' => 'authorName';

        $attributes = $entity->getAttributes();
        foreach ($attributes as $attribute) {
            yield $attribute->getAttributeKeyHandle() => $attribute->getAttributeKeyDisplayName();
        }
    }

}
