<?
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('page_list');
class DashboardSitemapSearchController extends Controller {

	public function view() {
		$html = Loader::helper('html');
		$form = Loader::helper('form');
		$this->set('form', $form);
		$pageList = $this->getRequestedSearchResults();
		$this->addHeaderItem('<script type="text/javascript">$(function() { ccm_setupAdvancedSearch(\'page\'); });</script>');
		$pages = $pageList->getPage();
				
		$this->set('pageList', $pageList);		
		$this->set('pages', $pages);		
		$this->set('pagination', $pageList->getPagination());
	}
	
	public function getRequestedSearchResults() {
		$pageList = new PageList();
		$pageList->enableStickySearchRequest();
		
		if ($_REQUEST['submit_search']) {
			$pageList->resetSearchRequest();
		}

		$req = $pageList->getSearchRequest();
		$pageList->displayUnapprovedPages();
		
		$keywords = htmlentities($req['keywords'], ENT_QUOTES, APP_CHARSET);
		$cvName = htmlentities($req['cvName'], ENT_QUOTES, APP_CHARSET);
		
		if ($keywords != '') {
			$pageList->filterByKeywords($keywords);
		}

		if ($cvName != '') {
			$pageList->filterByName($cvName);
		}

		if ($req['numResults']) {
			$pageList->setItemsPerPage($req['numResults']);
		}

		if ($req['ctID']) {
			$pageList->filterByCollectionTypeID($req['ctID']);
		}

		if (is_array($req['selectedSearchField'])) {
			foreach($req['selectedSearchField'] as $i => $item) {
				// due to the way the form is setup, index will always be one more than the arrays
				if ($item != '') {
					switch($item) {
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
						case 'version_status':
							if (isset($req['_cvIsApproved'])) {
								$req['cvIsApproved'] = $req['_cvIsApproved'];
							}
							$pageList->filterByIsApproved($req['cvIsApproved']);
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

?>