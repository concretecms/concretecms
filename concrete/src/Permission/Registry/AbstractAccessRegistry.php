<?php
namespace Concrete\Core\Permission\Registry;

use Concrete\Core\Permission\Registry\Entry\Access\EntryInterface;

/**
 * @since 8.0.0
 */
abstract class AbstractAccessRegistry implements AccessRegistryInterface
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

    /**
     * @since 8.2.0
     */
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
     * @since 8.2.0
     */
    public function getEntriesToRemove()
    {
        return $this->entriesToRemove;
    }



}
