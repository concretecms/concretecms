<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Express;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Tree\Tree;
use Loader;
use Core;
use Concrete\Core\Tree\Type\ExpressEntryResults;
use Concrete\Core\Tree\Node\Node as TreeNode;
use Concrete\Core\Tree\Node\Type\Topic as TopicTreeNode;
use Concrete\Core\Tree\Node\Type\Category as CategoryTreeNode;
use Permissions;

class Entries extends DashboardPageController
{
    public function view($treeID = false)
    {
        $tree = ExpressEntryResults::get();
        $this->set('tree', $tree);
        $this->requireAsset('core/topics');
    }


}
