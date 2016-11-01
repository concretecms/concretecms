<?php
namespace Concrete\Controller\Search;

use Controller;
use GroupList;
use Concrete\Core\User\Group\GroupSearchColumnSet;
use Concrete\Core\Search\Result\Result as SearchResult;
use Loader;
use TaskPermission;
use URL;

class Groups extends Controller
{
    protected $fields = [];

    public function __construct()
    {
        parent::__construct();
        $this->groupList = new GroupList();
    }

    public function search()
    {
        $tp = new TaskPermission();
        if (!$tp->canAccessGroupSearch()) {
            return false;
        }

        if ($this->request->request('filter') == 'assign') {
            $this->groupList->filterByAssignable();
        } else {
            $this->groupList->includeAllGroups();
        }

        $keywords = $this->request->request('keywords');
        if (isset($keywords)) {
            $this->groupList->filterByKeywords($keywords);
        }

        $this->groupList->sortBy('gID', 'asc');

        $columns = new GroupSearchColumnSet();
        $ilr = new SearchResult($columns, $this->groupList, URL::to('/ccm/system/search/groups/submit'));
        $this->result = $ilr;
    }

    public function getSearchResultObject()
    {
        return $this->result;
    }

    public function getListObject()
    {
        return $this->groupList;
    }

    public function submit()
    {
        $this->search();
        $result = $this->result;
        Loader::helper('ajax')->sendResult($this->result->getJSONObject());
    }
}
