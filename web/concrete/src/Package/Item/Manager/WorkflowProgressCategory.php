<?php
namespace Concrete\Core\Package\Item\Manager;

use Concrete\Core\Entity\Package;
use Concrete\Core\Workflow\Progress\Category;

defined('C5_EXECUTE') or die("Access Denied.");

class WorkflowProgressCategory extends AbstractItem
{

    public function getItemCategoryDisplayName()
    {
        return t('Workflow Progress Categories');
    }

    public function getItemName($category)
    {
        $txt = \Core::make('helper/text');
        return $txt->unhandle($category->getWorkflowProgressCategoryHandle());
    }

    public function getPackageItems(Package $package)
    {
        return Category::getListByPackage($package);
    }

}
