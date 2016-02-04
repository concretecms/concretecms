<?php
namespace Concrete\Core\Package\Item\Manager;

use Concrete\Core\Entity\Package;
use Concrete\Core\Page\PageList;

defined('C5_EXECUTE') or die("Access Denied.");

class Page extends AbstractItem
{

    public function getItemCategoryDisplayName()
    {
        return t('Pages');
    }

    public function getItemName($page)
    {
        return $page->getCollectionPath();
    }

    public function getPackageItems(Package $package)
    {
        $list = new PageList();
        $list->filterByPackage($package);
        return $list->getResults();
    }

}
