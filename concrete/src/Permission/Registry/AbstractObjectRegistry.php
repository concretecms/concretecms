<?php
namespace Concrete\Core\Permission\Registry;

use Concrete\Core\Permission\Registry\Entry\Object\EntryInterface;

abstract class AbstractObjectRegistry implements ObjectRegistryInterface
{

    protected $entries = [];
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
