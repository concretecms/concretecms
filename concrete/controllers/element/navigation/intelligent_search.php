<?php
namespace Concrete\Controller\Element\Navigation;

use Concrete\Core\Application\UserInterface\Dashboard\Navigation\FullNavigationFactory;
use Concrete\Core\Controller\ElementController;
use Concrete\Core\Navigation\Modifier\FlatChildrenModifier;
use Concrete\Core\Navigation\NavigationModifier;

class IntelligentSearch extends ElementController
{
    /**
     * @var FullNavigationFactory
     */
    protected $factory;

    public function getElement()
    {
        return 'navigation/intelligent_search';
    }

    public function __construct(FullNavigationFactory $factory)
    {
        $this->factory = $factory;
    }


    public function view()
    {
        $navigation = $this->factory->createNavigation();
        $modifier = new NavigationModifier();
        $modifier->addModifier(new FlatChildrenModifier());
        $this->set('navigation', $modifier->process($navigation));
    }
}