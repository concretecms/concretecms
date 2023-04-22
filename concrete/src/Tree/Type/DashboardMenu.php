<?php

namespace Concrete\Core\Tree\Type;

use Concrete\Core\Tree\Node\Type\MenuCategory;

class DashboardMenu extends Menu
{

    public function getTreeName()
    {
        return t('Dashboard Menu');
    }

    public static function add()
    {
        $rootNode = MenuCategory::add();
        $treeID = parent::create($rootNode);
        $tree = self::getByID($treeID);
        return $tree;
    }


}
