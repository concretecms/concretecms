<?
namespace Concrete\Controller\Search;
use Controller;
use FileList;
use \Concrete\Core\File\Search\ColumnSet\ColumnSet as FileSearchColumnSet;
use \Concrete\Core\File\Search\Result\Result as FileSearchResult;
use \Concrete\Core\Attribute\File\FileKey as FileAttributeKey;
use Permissions;
use Loader;
use FileSet;
use URL;
use FilePermissions;
use stdClass;

class Files extends Controller {

	protected $fields = array();

	public function __construct() {
		$this->fileList = new FileList();
		$this->fileList->enableStickySearchRequest();
	}

	public function search() {
		$cp = FilePermissions::getGlobal();
		if (!$cp->canSearchFiles()) {
			return false;
		}
		
		if ($_REQUEST['submitSearch']) {
			$this->fileList->resetSearchRequest();
		}

		$req = $this->fileList->getSearchRequest();
		$this->fileList->displayUnapprovedPages();
		$columns = FileSearchColumnSet::getCurrent();

		$col = $columns->getDefaultSortColumn();	
		$this->fileList->sortBy($col->getColumnKey(), $col->getColumnDefaultSortDirection());
		
		// first thing, we check to see if a saved search is being used
		if (isset($req['fssID'])) {
			$fs = FileSet::getByID($req['fssID']);
			if ($fs->getFileSetType() == FileSet::TYPE_SAVED_SEARCH) {
				$req = $fs->getSavedSearchRequest();
				$columns = $fs->getSavedSearchColumns();
				$colsort = $columns->getDefaultSortColumn();
				$this->fileList->addToSearchRequest('ccm_order_dir', $colsort->getColumnDefaultSortDirection());
				$this->fileList->addToSearchRequest('ccm_order_by', $colsort->getColumnKey());
			}
		}

		$keywords = htmlentities($req['fKeywords'], ENT_QUOTES, APP_CHARSET);
		
		if ($keywords != '') {
			$this->fileList->filterByKeywords($keywords);
		}

		if ($req['numResults']) {
			$this->fileList->setItemsPerPage(intval($req['numResults']));
		}
		
		if ((isset($req['fsIDNone']) && $req['fsIDNone'] == 1) || (is_array($req['fsID']) && in_array(-1, $req['fsID']))) { 
			$this->fileList->filterBySet(false);
		} else {
			if (is_array($req['fsID'])) {
				foreach($req['fsID'] as $fsID) {
					$fs = FileSet::getByID($fsID);
					$this->fileList->filterBySet($fs);
				}
			} else if (isset($req['fsID']) && $req['fsID'] != '' && $req['fsID'] > 0) {
				$set = $req['fsID'];
				$fs = FileSet::getByID($set);
				$this->fileList->filterBySet($fs);
			}
		}
		
		if (isset($req['fType']) && $req['fType'] != '') {
			$type = $req['fType'];
			$this->fileList->filterByType($type);
		}

		if (isset($req['fExtension']) && $req['fExtension'] != '') {
			$ext = $_GET['fExtension'];
			$fileList->filterByExtension($ext);
		}
		
		$selectedSets = array();
		if (is_array($req['field'])) {
			foreach($req['field'] as $i => $item) {
				$this->fields[] = $this->getField($item);
				// due to the way the form is setup, index will always be one more than the arrays
				if ($item != '') {
					switch($item) {
						case "extension":
							$extension = $req['extension'];
							$this->fileList->filterByExtension($extension);
							break;
						case "type":
							$type = $req['type'];
							$this->fileList->filterByType($type);
							break;
						case "date_added":
							$dateFrom = $req['date_from'];
							$dateTo = $req['date_to'];
							if ($dateFrom != '') {
								$dateFrom = date('Y-m-d', strtotime($dateFrom));
								$this->fileList->filterByDateAdded($dateFrom, '>=');
								$dateFrom .= ' 00:00:00';
							}
							if ($dateTo != '') {
								$dateTo = date('Y-m-d', strtotime($dateTo));
								$dateTo .= ' 23:59:59';
								
								$this->fileList->filterByDateAdded($dateTo, '<=');
							}
							break;
						case 'added_to':
							$ocID = $req['ocIDSearchField'];
							if ($ocID > 0) {
								$this->fileList->filterByOriginalPageID($ocID);							
							}
							break;
						case "size":
							$from = $req['size_from'];
							$to = $req['size_to'];
							$this->fileList->filterBySize($from, $to);
							break;
						default:
							$akID = $item;
							$fak = FileAttributeKey::get($akID);
							$type = $fak->getAttributeType();
							$cnt = $type->getController();
							$cnt->setRequestArray($req);
							$cnt->setAttributeKey($fak);
							$cnt->searchForm($this->fileList);
							break;
					}
				}
			}
		}
		if (isset($req['numResults'])) {
			$this->fileList->setItemsPerPage(intval($req['numResults']));
		}

		$ilr = new FileSearchResult($columns, $this->fileList, URL::to('/system/search/files/submit'), $this->fields);
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
			case 'size': ?>
				<?=$form->text('size_from', $searchRequest['size_from'], array('style' => 'width:  60px'))?>
				<?=t('to')?>
				<?=$form->text('size_to', $searchRequest['size_to'], array('style' => 'width: 60px'))?>
				<? break;
			case 'type':
				$t1 = FileList::getTypeList();
				$types = array();
				foreach($t1 as $value) {
					$types[$value] = FileType::getGenericTypeText($value);
				}
				print $form->select('types', $types, $searchRequest['types'], array('style' => 'width: 120px'));
				break;
			case 'extension':
				$ext1 = FileList::getExtensionList();
				$extensions = array();
				foreach($ext1 as $value) {
					$extensions[$value] = $value;
				}				
				print $form->select('extensions', $extensions, $searchRequest['extensions'], array('style' => 'width: 120px'));
				break;
			case 'date_added': ?>
				<?=$form->text('date_from', $searchRequest['date_from'], array('style' => 'width: 86px'))?>
				<?=t('to')?>
				<?=$form->text('date_to', $searchRequest['date_to'], array('style' => 'width: 86px'))?>
				<? break;
			case 'added_to': ?>
				<? $ps = Loader::helper("form/page_selector");
				print $ps->selectPage('ocIDSearchField');
				break;
			default: 
				if (Loader::helper('validation/numbers')->integer($field)) {
					$ak = FileAttributeKey::getByID($field);
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
		return $this->fileList->getSearchRequest();
	}


	
}

