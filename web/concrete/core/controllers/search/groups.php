<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Search_Groups extends Controller {

	protected $fields = array();

	public function __construct() {
		$this->groupList = new GroupSearch();
	}

	public function search() {
		$tp = new TaskPermission();
		if (!$tp->canAccessGroupSearch()) {
			return false;
		}

		if ($_REQUEST['filter'] == 'assign') {
			$this->groupList->filterByAssignable();
		} else {
			$this->groupList->includeAllGroups();
		}

		if (isset($_REQUEST['keywords'])) {
			$this->groupList->filterByKeywords($_REQUEST['keywords']);
		}
		
		$this->groupList->sortBy('gID', 'asc');

		$columns = new GroupSearchColumnSet();
		$ilr = new SearchResult($columns, $this->groupList, URL::to('/system/search/groups/submit'));
		$this->result = $ilr;
	}

	public function getSearchResultObject() {
		return $this->result;
	}

	public function submit() {
		$this->search();
		$result = $this->result;
		Loader::helper('ajax')->sendResult($this->result->getJSONObject());
	}

	public function getSearchRequest() {
		return $this->groupList->getSearchRequest();
	}


	
}

