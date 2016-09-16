<?php
namespace Concrete\Core\Permission\Registry;

use Concrete\Core\Permission\Registry\Entry\EntryInterface;

abstract class AbstractRegistry implements RegistryInterface
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
