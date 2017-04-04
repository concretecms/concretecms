<?php
namespace Concrete\Core\Application\UserInterface\Sitemap\TreeCollection;

use Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\Entry\EntryInterface;
use Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\Entry\EntryGroupInterface;
use Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\Entry\Group\GroupInterface;

interface TreeCollectionInterface
{

    /**
     * @return EntryInterface
     */
    function getEntries();

    /**
     * @return GroupInterface
     */
    function getEntryGroups();


    /**
     * @return bool
     */
    function displayMenu();



}
