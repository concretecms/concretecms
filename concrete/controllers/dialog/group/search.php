<?php
namespace Concrete\Controller\Dialog\Group;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Controller\Search\Groups as SearchGroupsController;
use Concrete\Core\Tree\Type\Group as GroupTree;
use Concrete\Core\Legacy\TaskPermission;
use Concrete\Core\Legacy\Loader;

class Search extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/group/search';

    protected function canAccess()
    {
        $tp = new TaskPermission();

        return $tp->canAccessGroupSearch();
    }

    public function view()
    {
        $cnt = new SearchGroupsController();
        $cnt->search();
        $result = Loader::helper('json')->encode($cnt->getSearchResultObject()->getJSONObject());
        $this->set('result', $result);
        $this->set('searchController', $cnt);
        $this->set('tree', GroupTree::get());
        $this->requireAsset('core/groups');
    }
}
