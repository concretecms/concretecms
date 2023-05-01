<?php
namespace Concrete\Core\Application\UserInterface\Dashboard\Navigation;

use Concrete\Core\Application\UserInterface\Dashboard\Navigation\Traits\CheckPageForInclusionInMenuTrait;
use Concrete\Core\Navigation\Item\DashboardPageItem;
use Concrete\Core\Navigation\Item\ItemInterface;
use Concrete\Core\Navigation\NavigationInterface;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\NavigationMenuNodeGroupInterface;
use Concrete\Core\Tree\Node\Type\NavigationMenuNodeInterface;
use Concrete\Core\Tree\Node\Type\Page;
use Concrete\Core\Tree\Type\MenuInterface;

class TreeMenuNavigationFactory
{

    use CheckPageForInclusionInMenuTrait;

    public function createNavigation(MenuInterface $menu): NavigationInterface
    {
        $navigation = new Navigation();
        $rootNode = $menu->getRootTreeNodeObject();
        $rootNode->populateChildren();
        $this->buildNavigation($navigation, $rootNode);
        return $navigation;
    }

    private function addItem($navigationOrItem, ItemInterface $item)
    {
        if ($navigationOrItem instanceof NavigationInterface) {
            $navigationOrItem->add($item);
        } else {
            $navigationOrItem->addChild($item);
        }
    }

    private function buildNavigation($navigationOrItem, Node $node): void
    {
        foreach ($node->getChildNodes() as $childNode) {
            if ($childNode instanceof NavigationMenuNodeGroupInterface) {
                $groupNavigation = $childNode->getNavigation();
                foreach ($groupNavigation->getItems() as $item) {
                    $this->addItem($navigationOrItem, $item);
                }
            }

            if (!$childNode instanceof NavigationMenuNodeInterface) {
                continue;
            }

            if (!$childNode->canViewNavigationItem()) {
                continue;
            }

            $item = $childNode->getNavigationItem();
            $this->addItem($navigationOrItem, $item);
            if (count($childNode->getChildNodes())) {
                $this->buildNavigation($item, $childNode);
            } else if ($childNode instanceof Page && $childNode->includeSubpagesInMenu()) {
                // Let's dynamically add the pages from the sitemap instead of from our menu.
                $page = $childNode->getTreeNodePageObject();
                foreach ($page->getCollectionChildren() as $child) {
                    if ($this->includePageInMenu($child)) {
                        $item->addChild(new DashboardPageItem($child));
                    }
                }
            }
        }

    }



    }
