<?php

namespace Concrete\Core\Application\UserInterface\Sitemap\TreeCollection;

use Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\Entry\EntryInterface;
use Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\Entry\Group\GroupInterface;

interface TreeCollectionInterface
{
    /**
     * @return EntryInterface
     */
    public function getEntries();

    /**
     * @return GroupInterface
     */
    public function getEntryGroups();

    /**
     * @return bool
     */
    public function displayMenu();
}
