<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Search_Pages extends Controller {

	protected $fields = array();

	public function __construct() {
		$this->pageList = new PageList();
		$this->pageList->enableStickySearchRequest();
	}

	public function search() {
		$dh = Loader::helper('concrete/dashboard/sitemap');
		if (!$dh->canRead()) {
			return false;
		}
		
		$this->pageList->ignoreAliases();
		
		if ($_REQUEST['submitSearch']) {
			$this->pageList->resetSearchRequest();
		}

		$req = $this->pageList->getSearchRequest();
		$this->pageList->displayUnapprovedPages();
		$columns = PageSearchColumnSet::getCurrent();

		$col = $columns->getDefaultSortColumn();	
		$this->pageList->sortBy($col->getColumnKey(), $col->getColumnDefaultSortDirection());
		
		$cvName = htmlentities($req['cvName'], ENT_QUOTES, APP_CHARSET);
		
		if ($cvName != '') {
			$this->pageList->filterByName($cvName);
		}

		if ($req['numResults'] && Loader::helper('validation/numbers')->integer($req['numResults'])) {
			$this->pageList->setItemsPerPage($req['numResults']);
		}

		if ($req['ptID']) {
			$this->pageList->filterByPageTypeID($req['ptID']);
		}

		if (is_array($req['field'])) {
			foreach($req['field'] as $i => $item) {
				$this->fields[] = $this->getField($item);
				// due to the way the form is setup, index will always be one more than the arrays
				if ($item != '') {
					switch($item) {
						case 'keywords':
							$keywords = htmlentities($req['keywords'], ENT_QUOTES, APP_CHARSET);
							$this->pageList->filterByKeywords($keywords);
							break;
						case 'num_children':
							$symbol = '=';
							if ($req['cChildrenSelect'] == 'gt') {
								$symbol = '>';
							} else if ($req['cChildrenSelect'] == 'lt') {
								$symbol = '<';
							}
							$this->pageList->filterByNumberOfChildren($req['cChildren'], $symbol);						
							break;
						case 'owner':
							$ui = UserInfo::getByUserName($req['owner']);
							if (is_object($ui)) {
								$this->pageList->filterByUserID($ui->getUserID());
							} else {
								$this->pageList->filterByUserID(-1);
							}
							break;
						case 'theme':
							$this->pageList->filter('pThemeID', $req['pThemeID']);
							break;
						case 'parent':
							if (isset($req['_cParentAll'])) {
								$req['cParentAll'] = $req['_cParentAll'];
							}
							if ($req['cParentIDSearchField'] > 0) {
								if ($req['cParentAll'] == 1) {
									$pc = Page::getByID($req['cParentIDSearchField']);
									$cPath = $pc->getCollectionPath();
									$this->pageList->filterByPath($cPath);
								} else {
									$this->pageList->filterByParentID($req['cParentIDSearchField']);
								}
							}
							break;
						case 'version_status':
							if (isset($req['_cvIsApproved'])) {
								$req['cvIsApproved'] = $req['_cvIsApproved'];
							}
							$this->pageList->filterByIsApproved($req['cvIsApproved']);
							break;
						case 'permissions_inheritance':
							$this->pageList->filter('cInheritPermissionsFrom', $req['cInheritPermissionsFrom']);
							break;
						case "date_public":
							$dateFrom = $req['date_public_from'];
							$dateTo = $req['date_public_to'];
							if ($dateFrom != '') {
								$dateFrom = date('Y-m-d', strtotime($dateFrom));
								$this->pageList->filterByPublicDate($dateFrom, '>=');
								$dateFrom .= ' 00:00:00';
							}
							if ($dateTo != '') {
								$dateTo = date('Y-m-d', strtotime($dateTo));
								$dateTo .= ' 23:59:59';								
								$this->pageList->filterByPublicDate($dateTo, '<=');
							}
							break;
						case "last_modified":
							$dateFrom = $req['last_modified_from'];
							$dateTo = $req['last_modified_to'];
							if ($dateFrom != '') {
								$dateFrom = date('Y-m-d', strtotime($dateFrom));
								$this->pageList->filterByDateLastModified($dateFrom, '>=');
								$dateFrom .= ' 00:00:00';
							}
							if ($dateTo != '') {
								$dateTo = date('Y-m-d', strtotime($dateTo));
								$dateTo .= ' 23:59:59';								
								$this->pageList->filterByDateLastModified($dateTo, '<=');
							}
							break;
						case "date_added":
							$dateFrom = $req['date_added_from'];
							$dateTo = $req['date_added_to'];
							if ($dateFrom != '') {
								$dateFrom = date('Y-m-d', strtotime($dateFrom));
								$this->pageList->filterByDateAdded($dateFrom, '>=');
								$dateFrom .= ' 00:00:00';
							}
							if ($dateTo != '') {
								$dateTo = date('Y-m-d', strtotime($dateTo));
								$dateTo .= ' 23:59:59';								
								$this->pageList->filterByDateAdded($dateTo, '<=');
							}
							break;

						default:
							$akID = $item;
							$fak = CollectionAttributeKey::get($akID);
							if (!is_object($fak) || (!($fak instanceof CollectionAttributeKey))) {
								break;
							}
							
							$type = $fak->getAttributeType();
							$cnt = $type->getController();
							$cnt->setRequestArray($req);
							$cnt->setAttributeKey($fak);
							$cnt->searchForm($this->pageList);
							break;
					}
				}
			}
		}

		$ilr = new PageSearchResult($columns, $this->pageList, URL::to('/system/search/pages/submit'), $this->fields);
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
		$searchRequest = $this->getSearchRequest();
		$form = Loader::helper('form');
		ob_start();
		switch($field) {
			case 'keywords':
				print $form->text('keywords', $searchRequest['keywords'], array('style' => 'width: 120px'));
				break;
			case 'date_public': ?>
				<?=$form->text('date_public_from', array('style' => 'width: 86px'))?>
				<?=t('to')?>
				<?=$form->text('date_public_from', array('style' => 'width: 86px'))?>
				<? break;
			case 'date_added': ?>
				<?=$form->text('date_added_from', array('style' => 'width: 86px'))?>
				<?=t('to')?>
				<?=$form->text('date_added_to', array('style' => 'width: 86px'))?>
				<? break;
			case 'last_modified': ?>
				<?=$form->text('last_modified_from', array('style' => 'width: 86px'))?>
				<?=t('to')?>
				<?=$form->text('last_modified_to', array('style' => 'width: 86px'))?>
				<? break;
			case 'owner': ?>
				<?=$form->text('owner', array('class'=>'span5'))?>
				<? break;
			case 'permissions_inheritance': ?>
				<select name="cInheritPermissionsFrom">
					<option value="PARENT"<? if ($req['cInheritPermissionsFrom'] == 'PARENT') { ?> selected <? } ?>><?=t('Parent Page')?></option>
					<option value="TEMPLATE" <? if ($req['cInheritPermissionsFrom'] == 'TEMPLATE') { ?> selected <? } ?>><?=t('Page Type')?></option>
					<option value="OVERRIDE"<? if ($req['cInheritPermissionsFrom'] == 'OVERRIDE') { ?> selected <? } ?>><?=t('Itself (Override)')?></option>
				</select>
				<? break;
			case 'version_status': ?>
				<label class="checkbox"><?=$form->radio('cvIsApproved', 0, false)?> <span><?=t('Unapproved')?></span></label>
				<label class="checkbox"><?=$form->radio('cvIsApproved', 1, false)?> <span><?=t('Approved')?></span></label>
				<? break;
			case 'parent': ?>
				<? $ps = Loader::helper("form/page_selector");
				print $ps->selectPage('cParentIDSearchField');
				?>
				
				<br/><strong><?=t('Search All Children?')?></strong><br/>
				<label class="checkbox"><?=$form->radio('cParentAll', 0, false)?> <span><?=t('No')?></span></label>
				<label class="checkbox"><?=$form->radio('cParentAll', 1, false)?> <span><?=t('Yes')?></span></label>
				<? break;
			case 'num_children': ?>
				<select name="cChildrenSelect">
					<option value="gt"<? if ($req['cChildrenSelect'] == 'gt') { ?> selected <? } ?>><?=t('More Than')?></option>
					<option value="eq" <? if ($req['cChildrenSelect'] == 'eq') { ?> selected <? } ?>><?=t('Equal To')?></option>
					<option value="lt"<? if ($req['cChildrenSelect'] == 'lt') { ?> selected <? } ?>><?=t('Fewer Than')?></option>
				</select>
				<input type="text" name="cChildren" value="<?=$req['cChildren']?>" />
				<? break;
			case 'theme': ?>
				<select name="pThemeID">
				<? $themes = PageTheme::getList(); ?>
				<? foreach($themes as $pt) { ?>
					<option value="<?=$pt->getThemeID()?>" <? if ($pt->getThemeID() == $searchRequest['pThemeID']) { ?> selected<? } ?>><?=$pt->getThemeName()?></option>			
				<? } ?>
				</select>
				<? break;
			default: 
				if (Loader::helper('validation/numbers')->integer($field)) {
					$ak = CollectionAttributeKey::getByID($field);
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

	public function getSearchRequest() {
		return $this->pageList->getSearchRequest();
	}


	
}

