<?php

namespace Concrete\Core\Package\ItemCategory;

use Concrete\Controller\Element\Package\ThemeItemList;
use Concrete\Core\Entity\Package;

defined('C5_EXECUTE') or die('Access Denied.');

class Theme extends AbstractCategory
{
    public function getItemCategoryDisplayName()
    {
        return t('Themes');
    }

    public function getItemName($theme)
    {
        return $theme->getThemeName();
    }

    public function renderList(Package $package)
    {
        $controller = new ThemeItemList($this, $package);
        $controller->render();
    }

    public function getPackageItems(Package $package)
    {
        return \Concrete\Core\Page\Theme\Theme::getListByPackage($package);
    }
}
