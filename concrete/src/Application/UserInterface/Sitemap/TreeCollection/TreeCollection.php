<?php

namespace Concrete\Core\Application\UserInterface\Sitemap\TreeCollection;

use Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\Entry\EntryInterface;
use Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\Entry\Group\GroupInterface;

abstract class TreeCollection implements TreeCollectionInterface
{
    /**
     * @var \Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\Entry\EntryInterface[]
     */
    protected $entries = [];

    /**
     * @var \Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\Entry\Group\GroupInterface[]
     */
    protected $entryGroups = [];

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\TreeCollectionInterface::getEntries()
     */
    public function getEntries()
    {
        return $this->entries;
    }

    /**
     * Add an entry to the entry list.
     *
     * @param \Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\Entry\EntryInterface $entry
     */
    public function addEntry(EntryInterface $entry)
    {
        $this->entries[] = $entry;
    }

    /**
     * Add a group to the group list.
     *
     * @param \Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\Entry\Group\GroupInterface $group
     */
    public function addEntryGroup(GroupInterface $group)
    {
        $this->entryGroups[] = $group;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\TreeCollectionInterface::getEntryGroups()
     */
    public function getEntryGroups()
    {
        return $this->entryGroups;
    }
}
