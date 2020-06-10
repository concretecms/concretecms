<?php
namespace Concrete\Controller\Element\Dashboard\Navigation\Panel;

use Concrete\Core\Application\UserInterface\Dashboard\Navigation\FullNavigationFactory;
use Concrete\Core\Controller\ElementController;
use Concrete\Core\Navigation\Modifier\TopLevelOnlyModifier;
use Concrete\Core\Navigation\NavigationModifier;

class Top extends ElementController
{

    /**
     * @var FullNavigationFactory
     */
    protected $factory;

    public function getElement()
    {
        return 'dashboard/navigation/nav';
    }

    public function __construct(FullNavigationFactory $factory)
    {
        $this->factory = $factory;
    }


    public function view()
    {
        $navigation = $this->factory->createNavigation();
        $modifier = new NavigationModifier();
        $modifier->addModifier(new TopLevelOnlyModifier());
        $this->set('navigation', $modifier->process($navigation));
    }

}
