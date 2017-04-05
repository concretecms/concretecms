<?php
namespace Concrete\Core\Application\UserInterface\Sitemap\TreeCollection;

use Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\Entry\EntryInterface;
use Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\Entry\Group\GroupInterface;

abstract class TreeCollection implements TreeCollectionInterface
{

    /**
     * @var EntryInterface[]
     */
    protected $entries = [];

    protected $entryGroups = [];

    /**
     * @return array
     */
    public function getEntries()
    {
        return $this->entries;
    }

    public function addEntry(EntryInterface $entry)
    {
        $this->entries[] = $entry;
    }

    public function addEntryGroup(GroupInterface $group)
    {
        $this->entryGroups[] = $group;
    }


    /**
     * @return array
     */
    public function getEntryGroups()
    {
        return $this->entryGroups;
    }

}
