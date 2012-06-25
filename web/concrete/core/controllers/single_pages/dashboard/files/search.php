<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_Files_Search extends Controller {

	public function view() {
		$html = Loader::helper('html');
		$form = Loader::helper('form');
		$this->set('form', $form);
		$searchInstance = 'file' . time();
		$this->addHeaderItem('<script type="text/javascript">$(function() { ccm_activateFileManager(\'DASHBOARD\', \'' . $searchInstance . '\'); });</script>');
		$fileList = $this->getRequestedSearchResults();
		$files = $fileList->getPage();
				
		$this->set('fileList', $fileList);		
		$this->set('files', $files);		
		$this->set('searchInstance', $searchInstance);		
		$this->set('pagination', $fileList->getPagination());
	}
	
	public function getRequestedSearchResults() {
		$fileList = new FileList();
		$fileList->enableStickySearchRequest();
				
		Loader::model('file_set');
		
		if ($_REQUEST['submit_search']) {
			$fileList->resetSearchRequest();
		}

		$req = $fileList->getSearchRequest();
		
		// first thing, we check to see if a saved search is being used
		if (isset($_REQUEST['fssID'])) {
			$fs = FileSet::getByID($_REQUEST['fssID']);
			if ($fs->getFileSetType() == FileSet::TYPE_SAVED_SEARCH) {
				$req = $fs->getSavedSearchRequest();
				$columns = $fs->getSavedSearchColumns();
				$colsort = $columns->getDefaultSortColumn();
				$fileList->addToSearchRequest('ccm_order_dir', $colsort->getColumnDefaultSortDirection());
				$fileList->addToSearchRequest('ccm_order_by', $colsort->getColumnKey());
			}
		}
		
		if (!isset($columns)) {
			$columns = FileManagerColumnSet::getCurrent();
		}

		$this->set('searchRequest', $req);
		$this->set('columns', $columns);

		$col = $columns->getDefaultSortColumn();	
		$fileList->sortBy($col->getColumnKey(), $col->getColumnDefaultSortDirection());
		
		$keywords = htmlentities($req['fKeywords'], ENT_QUOTES, APP_CHARSET);
		
		if ($keywords != '') {
			$fileList->filterByKeywords($keywords);
		}

		if ($req['numResults']) {
			$fileList->setItemsPerPage($req['numResults']);
		}
		
		if ((isset($req['fsIDNone']) && $req['fsIDNone'] == 1) || (is_array($req['fsID']) && in_array(-1, $req['fsID']))) { 
			$fileList->filterBySet(false);
		} else {
			if (is_array($req['fsID'])) {
				foreach($req['fsID'] as $fsID) {
					$fs = FileSet::getByID($fsID);
					$fileList->filterBySet($fs);
				}
			} else if (isset($req['fsID']) && $req['fsID'] != '' && $req['fsID'] > 0) {
				$set = $req['fsID'];
				$fs = FileSet::getByID($set);
				$fileList->filterBySet($fs);
			}
		}
		
		if (isset($_GET['fType']) && $_GET['fType'] != '') {
			$type = $_GET['fType'];
			$fileList->filterByType($type);
		}

		if (isset($_GET['fExtension']) && $_GET['fExtension'] != '') {
			$ext = $_GET['fExtension'];
			$fileList->filterByExtension($ext);
		}
		
		$selectedSets = array();

		if (is_array($req['selectedSearchField'])) {
			foreach($req['selectedSearchField'] as $i => $item) {
				// due to the way the form is setup, index will always be one more than the arrays
				if ($item != '') {
					switch($item) {
						case "extension":
							$extension = $req['extension'];
							$fileList->filterByExtension($extension);
							break;
						case "type":
							$type = $req['type'];
							$fileList->filterByType($type);
							break;
						case "date_added":
							$dateFrom = $req['date_from'];
							$dateTo = $req['date_to'];
							if ($dateFrom != '') {
								$dateFrom = date('Y-m-d', strtotime($dateFrom));
								$fileList->filterByDateAdded($dateFrom, '>=');
								$dateFrom .= ' 00:00:00';
							}
							if ($dateTo != '') {
								$dateTo = date('Y-m-d', strtotime($dateTo));
								$dateTo .= ' 23:59:59';
								
								$fileList->filterByDateAdded($dateTo, '<=');
							}
							break;
						case 'added_to':
							$ocID = $req['ocIDSearchField'];
							if ($ocID > 0) {
								$fileList->filterByOriginalPageID($ocID);							
							}
							break;
						case "size":
							$from = $req['size_from'];
							$to = $req['size_to'];
							$fileList->filterBySize($from, $to);
							break;
						default:
							Loader::model('file_attributes');
							$akID = $item;
							$fak = FileAttributeKey::get($akID);
							$type = $fak->getAttributeType();
							$cnt = $type->getController();
							$cnt->setRequestArray($req);
							$cnt->setAttributeKey($fak);
							$cnt->searchForm($fileList);
							break;
					}
				}
			}
		}
		if (isset($req['numResults'])) {
			$fileList->setItemsPerPage($req['numResults']);
		}
		return $fileList;
	}
}

?>