<?php
namespace Concrete\Core\Tree\Node\Type;

use Concrete\Core\Permission\Response\DashboardNavigationMenuTreeNodeResponse;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Tree\Node\Type\Menu\NavigationMenuMenu;

class NavigationMenu extends Category
{

    public function getPermissionResponseClassName()
    {
        return DashboardNavigationMenuTreeNodeResponse::class;
    }

    public function getTreeNodeDisplayName($format = 'html')
    {
        $app = Facade::getFacadeApplication();
        $vsh = $app->make('helper/validation/strings');
        if ($vsh->notempty($this->getTreeNodeName())) {
            $name = tc($this->getTreeNodeTranslationContext(), $this->getTreeNodeName());
            switch ($format) {
                case 'html':
                    return h($name);
                case 'text':
                default:
                    return $name;
            }
        } elseif ($this->treeNodeParentID == 0) {
            return t('Menu');
        }
    }

    public function getTreeNodeMenu()
    {
        return new NavigationMenuMenu($this);
    }


    public function getTreeNodeJSON()
    {
        $obj = parent::getTreeNodeJSON();
        if (is_object($obj)) {
            $obj->folder = true;
            $obj->checkbox = false;
            $obj->icon = 'fas fa-sitemap';
            return $obj;
        }
    }



}
