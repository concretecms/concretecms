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

		if ($_REQUEST['include_core_groups'] == 1) {
			$this->groupList->includeAllGroups();
		}
		if ($_REQUEST['filter'] == 'assign') {
			$pk = PermissionKey::getByHandle('assign_user_groups');
			$this->groupList->filterByAllowedPermission($pk);
		}
		if (isset($_GET['gKeywords'])) {
			$this->groupList->filterByKeywords($_GET['gKeywords']);
		}

		$ilr = new SearchResult($columns, $this->groupList, URL::to('/system/search/groups/submit'), $this->fields);
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
		return $this->userList->getSearchRequest();
	}


	
}

