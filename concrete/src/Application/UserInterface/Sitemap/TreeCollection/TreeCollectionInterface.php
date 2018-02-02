<?php

namespace Concrete\Core\Application\UserInterface\Sitemap\TreeCollection;

/**
 * Interface that all the sitemap tree collections (eg multilingual sections) must implement.
 */
interface TreeCollectionInterface
{
    /**
     * Get the list of sitemap entries.
     *
     * @return \Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\Entry\EntryInterface
     */
    public function getEntries();

    /**
     * Get the list of sitemap groups.
     *
     * @return \Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\Entry\Group\GroupInterface
     */
    public function getEntryGroups();

    /**
     * Should the site locales menu be displayed?
     *
     * @return bool
     */
    public function displayMenu();
}
