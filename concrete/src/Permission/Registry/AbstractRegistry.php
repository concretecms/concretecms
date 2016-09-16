<?php
namespace Concrete\Core\Permission\Registry;

abstract class AbstractRegistry
{

    protected $entries = [];

    public function addEntry(EntryInterface $entry)
    {
        $this->entries[] = $entry;
    }

    public function getEntries()
    {
        return $this->entries;
    }


}
