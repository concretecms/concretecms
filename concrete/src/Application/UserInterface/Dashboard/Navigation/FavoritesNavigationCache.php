<?php
namespace Concrete\Core\Application\UserInterface\Dashboard\Navigation;

use Concrete\Core\Navigation\Navigation as BaseNavigation;
use Symfony\Component\HttpFoundation\Session\Session;

class FavoritesNavigationCache extends AbstractNavigationCache
{

    public function getIdentifier(): string
    {
        return 'dashboard_favorites_menu';
    }



}
