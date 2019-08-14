<?php
namespace Concrete\Core\Permission\Registry;

use Concrete\Core\Permission\Registry\Entry\Object\EntryInterface;

/**
 * @since 8.0.0
 */
abstract class AbstractObjectRegistry implements ObjectRegistryInterface
{

    protected $entries = [];
    /**
     * @since 8.2.0
     */
    protected $entriesToRemove = [];

    public function addEntry(EntryInterface $entry)
    {
        $this->entries[] = $entry;
    }

    public function removeEntry(EntryInterface $entry)
    {
        $this->entriesToRemove[] = $entry;
    }


    public function getEntries()
    {
        return $this->entries;
    }

    /**
     * @return array
     */
    public function getEntriesToRemove()
    {
        return $this->entriesToRemove;
    }



}
