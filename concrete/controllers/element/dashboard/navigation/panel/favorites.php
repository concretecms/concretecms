<?php
namespace Concrete\Controller\Element\Dashboard\Navigation\Panel;

use Concrete\Core\Application\UserInterface\Dashboard\Navigation\FavoritesNavigationFactory;
use Concrete\Core\Application\UserInterface\Dashboard\Navigation\FullNavigationFactory;
use Concrete\Core\Controller\ElementController;
use Concrete\Core\Navigation\Modifier\TopLevelOnlyModifier;
use Concrete\Core\Navigation\NavigationModifier;

class Favorites extends ElementController
{

    /**
     * @var FullNavigationFactory
     */
    protected $factory;

    public function getElement()
    {
        return 'dashboard/navigation/nav';
    }

    public function __construct(FavoritesNavigationFactory $factory)
    {
        $this->factory = $factory;
    }


    public function view()
    {
        $navigation = $this->factory->createNavigation();
        $this->set('navigation', $navigation);
    }

}
