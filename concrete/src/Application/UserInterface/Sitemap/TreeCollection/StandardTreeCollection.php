<?php
namespace Concrete\Core\Application\UserInterface\Sitemap\TreeCollection;

use Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\Entry\EntryInterface;

class StandardTreeCollection extends TreeCollection
{

    public function displayMenu()
    {
        $entries = $this->getEntries();
        return count($entries) > 1;
    }

}
