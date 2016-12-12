<?php
namespace Concrete\Core\Permission\Registry;

use Concrete\Core\Permission\Registry\Entry\Access\EntryInterface;

abstract class AbstractAccessRegistry implements AccessRegistryInterface
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
