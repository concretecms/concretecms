<?php

namespace Concrete\Core\Application\UserInterface\Sitemap\TreeCollection;

class StandardTreeCollection extends TreeCollection
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\TreeCollectionInterface::displayMenu()
     */
    public function displayMenu()
    {
        $entries = $this->getEntries();

        return count($entries) > 1;
    }
}
