<?php

namespace Concrete\Core\Application\UserInterface\Sitemap\TreeCollection;

class StandardTreeCollection extends TreeCollection
{
    public function displayMenu()
    {
        $entries = $this->getEntries();

        return count($entries) > 1;
    }
}
