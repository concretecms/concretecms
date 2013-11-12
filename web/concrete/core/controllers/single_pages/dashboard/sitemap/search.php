<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Page_Dashboard_Sitemap_Search extends DashboardController {
	
	public $helpers = array('form');
	
	public function view() {

		$r = ResponseAssetGroup::get();
		$r->requireAsset('core/search');
		$this->addFooterItem("<script type=\"text/javascript\">$(function() { $('div[data-search=pages]').concreteAjaxSearch(); });</script>");
		$pageList = $this->getRequestedSearchResults();
		if (is_object($pageList)) {
			$pages = $pageList->getPage();
			$this->set('pageList', $pageList);		
			$this->set('pages', $pages);		
			$this->set('pagination', $pageList->getPagination());
		}
	}

	public function getRequestedSearchResults() {
	
		$dh = Loader::helper('concrete/dashboard/sitemap');
		if (!$dh->canRead()) {
			return false;
		}
		
		$pageList = new PageList();
		$pageList->ignoreAliases();
		$pageList->enableStickySearchRequest();
		
		if ($_REQUEST['submitSearch']) {
			$pageList->resetSearchRequest();
		}

		$req = $pageList->getSearchRequest();
		$pageList->displayUnapprovedPages();

		$columns = PageSearchColumnSet::getCurrent();
		$this->set('columns', $columns);
		
		$cvName = htmlentities($req['cvName'], ENT_QUOTES, APP_CHARSET);
		
		if ($cvName != '') {
			$pageList->filterByName($cvName);
		}

		if ($req['numResults'] && Loader::helper('validation/numbers')->integer($req['numResults'])) {
			$pageList->setItemsPerPage($req['numResults']);
		}

		if ($req['ptID']) {
			$pageList->filterByPageTypeID($req['ptID']);
		}

		if (is_array($req['selectedSearchField'])) {
			foreach($req['selectedSearchField'] as $i => $item) {
				// due to the way the form is setup, index will always be one more than the arrays
				if ($item != '') {
					switch($item) {
						case 'keywords':
							$keywords = htmlentities($req['keywords'], ENT_QUOTES, APP_CHARSET);
							$pageList->filterByKeywords($keywords);
							break;
						case 'num_children':
							$symbol = '=';
							if ($req['cChildrenSelect'] == 'gt') {
								$symbol = '>';
							} else if ($req['cChildrenSelect'] == 'lt') {
								$symbol = '<';
							}
							$pageList->filterByNumberOfChildren($req['cChildren'], $symbol);						
							break;
						case 'owner':
							$ui = UserInfo::getByUserName($req['owner']);
							if (is_object($ui)) {
								$pageList->filterByUserID($ui->getUserID());
							} else {
								$pageList->filterByUserID(-1);
							}
							break;
						case 'theme':
							$pageList->filter('pThemeID', $_REQUEST['pThemeID']);
							break;
						case 'parent':
							if (isset($req['_cParentAll'])) {
								$req['cParentAll'] = $req['_cParentAll'];
							}
							if ($req['cParentIDSearchField'] > 0) {
								if ($req['cParentAll'] == 1) {
									$pc = Page::getByID($req['cParentIDSearchField']);
									$cPath = $pc->getCollectionPath();
									$pageList->filterByPath($cPath);
								} else {
									$pageList->filterByParentID($req['cParentIDSearchField']);
								}
							}
							break;
						case 'version_status':
							if (isset($req['_cvIsApproved'])) {
								$req['cvIsApproved'] = $req['_cvIsApproved'];
							}
							$pageList->filterByIsApproved($req['cvIsApproved']);
							break;
						case 'permissions_inheritance':
							$pageList->filter('cInheritPermissionsFrom', $req['cInheritPermissionsFrom']);
							break;
						case "date_public":
							$dateFrom = $req['date_public_from'];
							$dateTo = $req['date_public_to'];
							if ($dateFrom != '') {
								$dateFrom = date('Y-m-d', strtotime($dateFrom));
								$pageList->filterByPublicDate($dateFrom, '>=');
								$dateFrom .= ' 00:00:00';
							}
							if ($dateTo != '') {
								$dateTo = date('Y-m-d', strtotime($dateTo));
								$dateTo .= ' 23:59:59';								
								$pageList->filterByPublicDate($dateTo, '<=');
							}
							break;
						case "last_modified":
							$dateFrom = $req['last_modified_from'];
							$dateTo = $req['last_modified_to'];
							if ($dateFrom != '') {
								$dateFrom = date('Y-m-d', strtotime($dateFrom));
								$pageList->filterByDateLastModified($dateFrom, '>=');
								$dateFrom .= ' 00:00:00';
							}
							if ($dateTo != '') {
								$dateTo = date('Y-m-d', strtotime($dateTo));
								$dateTo .= ' 23:59:59';								
								$pageList->filterByDateLastModified($dateTo, '<=');
							}
							break;
						case "date_added":
							$dateFrom = $req['date_added_from'];
							$dateTo = $req['date_added_to'];
							if ($dateFrom != '') {
								$dateFrom = date('Y-m-d', strtotime($dateFrom));
								$pageList->filterByDateAdded($dateFrom, '>=');
								$dateFrom .= ' 00:00:00';
							}
							if ($dateTo != '') {
								$dateTo = date('Y-m-d', strtotime($dateTo));
								$dateTo .= ' 23:59:59';								
								$pageList->filterByDateAdded($dateTo, '<=');
							}
							break;

						default:
							Loader::model('attribute/categories/collection');
							$akID = $item;
							$fak = CollectionAttributeKey::get($akID);
							if (!is_object($fak) || (!($fak instanceof CollectionAttributeKey))) {
								break;
							}
							
							$type = $fak->getAttributeType();
							$cnt = $type->getController();
							$cnt->setRequestArray($req);
							$cnt->setAttributeKey($fak);
							$cnt->searchForm($pageList);
							break;
					}
				}
			}
		}

		$this->set('searchRequest', $req);
		return $pageList;
	}
}