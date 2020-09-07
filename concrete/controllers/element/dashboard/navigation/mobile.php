<?php
namespace Concrete\Controller\Element\Dashboard\Navigation;

use Concrete\Core\Application\UserInterface\Dashboard\Navigation\FullNavigationFactory;
use Concrete\Core\Controller\ElementController;
use Concrete\Core\Navigation\NavigationModifier;

class Mobile extends ElementController
{

    /**
     * @var FullNavigationFactory
     */
    protected $factory;

    public function getElement()
    {
        return 'dashboard/navigation/mobile';
    }

    public function __construct(FullNavigationFactory $factory)
    {
        $this->factory = $factory;
    }


    public function view()
    {
        $navigation = $this->factory->createNavigation();
        $modifier = new NavigationModifier();
        $this->set('navigation', $modifier->process($navigation));
    }

}
