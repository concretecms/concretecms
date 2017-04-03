<?php
namespace Concrete\Core\Application\UserInterface\Sitemap\TreeCollection;

use Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\Entry\EntryInterface;
use Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\Entry\EntryGroupInterface;

interface TreeCollectionInterface
{

    /**
     * @return EntryInterface
     */
    function getEntries();

    /**
     * @return EntryGroupInterface
     */
    function getEntryGroups();


    /**
     * @return bool
     */
    function displayMenu();

}
