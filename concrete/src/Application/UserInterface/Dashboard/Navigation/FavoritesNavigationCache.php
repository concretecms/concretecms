<?php

namespace Concrete\Core\Application\UserInterface\Dashboard\Navigation;

class FavoritesNavigationCache extends AbstractNavigationCache
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Application\UserInterface\Dashboard\Navigation\AbstractNavigationCache::getIdentifier()
     */
    public function getIdentifier(): string
    {
        return 'dashboard_favorites_menu';
    }
}
