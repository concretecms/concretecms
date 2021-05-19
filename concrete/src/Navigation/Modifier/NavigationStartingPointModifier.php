<?php
namespace Concrete\Core\Navigation\Modifier;

use Concrete\Core\Navigation\Modifier\Traits\GetPageItemFromNavigationTrait;
use Concrete\Core\Navigation\NavigationInterface;
use Concrete\Core\Page\Page;

class NavigationStartingPointModifier implements ModifierInterface
{

    use GetPageItemFromNavigationTrait;

    /**
     * @var Page
     */
    protected $startingPage;

    public function __construct(Page $page)
    {
        $this->startingPage = $page;
    }

    public function modify(NavigationInterface $navigation)
    {
        $startingPageItem = $this->getPageItemFromNavigation($this->startingPage, $navigation->getItems());
        if ($startingPageItem) {
            $navigation->setItems($startingPageItem->getChildren());
        }
    }

}
