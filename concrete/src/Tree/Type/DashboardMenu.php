<?php

namespace Concrete\Core\Tree\Type;


use Concrete\Core\Tree\Node\Type\NavigationMenu;

class DashboardMenu extends Menu
{

    public function getTreeName()
    {
        return t('Dashboard Menu');
    }

    public static function add()
    {
        $rootNode = NavigationMenu::add();
        $treeID = parent::create($rootNode);
        $tree = self::getByID($treeID);
        return $tree;
    }


}
