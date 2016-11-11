<?php
namespace Concrete\Core\Permission\Registry;

use Concrete\Core\Permission\Registry\Entry\Object\EntryInterface;

abstract class AbstractObjectRegistry implements ObjectRegistryInterface
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
