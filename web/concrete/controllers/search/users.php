<?
namespace Concrete\Controller\Search;
use Concrete\Core\Search\StickyRequest;
use Concrete\Core\User\Group\GroupSetList;
use Controller;
use UserList;
use \Concrete\Core\User\Search\ColumnSet\ColumnSet as UserSearchColumnSet;
use \Concrete\Core\User\Search\Result\Result as UserSearchResult;
use GroupList;
use UserAttributeKey;
use Permissions;
use Loader;
use GroupSet;
use stdClass;
use User;
use URL;
use Group;

class Users extends Controller {

	protected $fields = array();

    /**
     * @var \Concrete\Core\User\UserList
     */
    protected $userList;

	public function __construct() {
        $this->searchRequest = new StickyRequest('users');
        $this->userList = new UserList($this->searchRequest);
	}

	public function search() {
		$dh = Loader::helper('concrete/dashboard/sitemap');
		if (!$dh->canRead()) {
			return false;
		}
		
		if ($_REQUEST['submitSearch']) {
			$this->searchRequest->resetSearchRequest();
		}

		$req = $this->searchRequest->getSearchRequest();
		$columns = UserSearchColumnSet::getCurrent();

        if (!$this->userList->getActiveSortColumn()) {
    		$col = $columns->getDefaultSortColumn();
	    	$this->userList->sortBy($col->getColumnKey(), $col->getColumnDefaultSortDirection());
        }

        $this->userList->includeInactiveUsers();
        $this->userList->includeUnvalidatedUsers();

        $columns = UserSearchColumnSet::getCurrent();
		$this->set('columns', $columns);

		if ($req['keywords'] != '') {
			$this->userList->filterByKeywords($req['keywords']);
		}	
		
		if ($req['numResults'] && Loader::helper('validation/numbers')->integer($req['numResults'])) {
			$this->userList->setItemsPerPage($req['numResults']);
		}
		
		$u = new User();
		if (!$u->isSuperUser()) {
			$gIDs = array(-1);
			$gs = new GroupList();
			$groups = $gs->getResults();
			foreach($groups as $g) {
				$gp = new Permissions($g);
				if ($gp->canSearchUsersInGroup()) {
					$gIDs[] = $g->getGroupID();
				}
			}
			$this->userList->addToQuery("left join UserGroups ugRequired on ugRequired.uID = u.uID ");	
			$this->userList->filter(false, '(ugRequired.gID in (' . implode(',', $gIDs) . ') or ugRequired.gID is null)');
		}
		
		$filterGIDs = array();
		if (isset($req['gID']) && is_array($req['gID'])) {
			foreach($req['gID'] as $gID) {
				$g = Group::getByID($gID);
				if (is_object($g)) {
					$gp = new Permissions($g);
					if ($gp->canSearchUsersInGroup()) {
						$filterGIDs[] = $g->getGroupID();
					}
				}
			}
		}
		
		foreach($filterGIDs as $gID) {
			$this->userList->filterByGroupID($gID);
		}
		
		if (is_array($req['field'])) {
			foreach($req['field'] as $i => $item) {
				$this->fields[] = $this->getField($item);
				// due to the way the form is setup, index will always be one more than the arrays
				if ($item != '') {
					switch($item) {
						case 'is_active':
							if ($req['active'] === '0') {
								$this->userList->filterByIsActive(0);
							} else if ($req['active'] === '1') {
								$this->userList->filterByIsActive(1);
							}
							break;
						case "date_added":
							$dateFrom = $_REQUEST['date_from'];
							$dateTo = $_REQUEST['date_to'];
							if ($dateFrom != '') {
								$dateFrom = date('Y-m-d', strtotime($dateFrom));
								$this->userList->filterByDateAdded($dateFrom, '>=');
								$dateFrom .= ' 00:00:00';
							}
							if ($dateTo != '') {
								$dateTo = date('Y-m-d', strtotime($dateTo));
								$dateTo .= ' 23:59:59';
								
								$this->userList->filterByDateAdded($dateTo, '<=');
							}
							break;
						case "group_set":
							$gsID = $_REQUEST['gsID'];
							$gs = GroupSet::getByID($gsID);
							$groupsetids = array(-1);
							if (is_object($gs)) {
								$groups = $gs->getGroups();
							}
							$this->userList->addToQuery('left join UserGroups ugs on u.uID = ugs.uID');
							foreach($groups as $g) {
								if ($pk->validate($g) && (!in_array($g->getGroupID(), $groupsetids))) {
									$groupsetids[] = $g->getGroupID();
								}								
							}							
							$instr = 'ugs.gID in (' . implode(',', $groupsetids) . ')';
							$this->userList->filter(false, $instr);
							break;

						default:
							$akID = $item;
							$fak = UserAttributeKey::getByID($akID);
							$type = $fak->getAttributeType();
							$cnt = $type->getController();
							$cnt->setAttributeKey($fak);
							$cnt->searchForm($this->userList);
							break;
					}
				}
			}
		}

		$ilr = new UserSearchResult($columns, $this->userList, URL::to('/ccm/system/search/users/submit'), $this->fields);
		$this->result = $ilr;
	}

	public function getSearchResultObject() {
		return $this->result;
	}

	public function field($field) {
		$r = $this->getField($field);
		Loader::helper('ajax')->sendResult($r);
	}

	protected function getField($field) {
		$r = new stdClass;
		$r->field = $field;
		$searchRequest = $this->searchRequest->getSearchRequest();
		$form = Loader::helper('form');
		ob_start();
		switch($field) {
			case 'date_added': ?>
				<?=$form->text('date_from', array('style' => 'width: 86px'))?>
				<?=t('to')?>
				<?=$form->text('date_to', array('style' => 'width: 86px'))?>
				<? break;
			case 'is_active':
				print $form->select('active', array('0' => t('Inactive Users'), '1' => t('Active Users')), array('style' => 'vertical-align: middle'));
				break;
			case 'group_set':
				$gsl = new GroupSetList();
				$groupsets = array();
				foreach($gsl->get() as $gs) { 
					$groupsets[$gs->getGroupSetID()] = $gs->getGroupSetName();
				}
				print $form->select('gsID', $groupsets);
				break;
			default:
				if (Loader::helper('validation/numbers')->integer($field)) {
					$ak = UserAttributeKey::getByID($field);
					$ak->render('search');
				}
				break;
		}
		$contents = ob_get_contents();
		ob_end_clean();
		$r->html = $contents;
		return $r;
	}

	public function submit() {
		$this->search();
		$result = $this->result;
		Loader::helper('ajax')->sendResult($this->result->getJSONObject());
	}

	public function getFields() {
		return $this->fields;		
	}

	
}

