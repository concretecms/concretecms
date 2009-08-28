<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('file_list');
Loader::model('file_set');
class DashboardFilesSearchController extends Controller {

	public function view() {
		$html = Loader::helper('html');
		$form = Loader::helper('form');
		$this->set('form', $form);
		$this->addHeaderItem('<script type="text/javascript">$(function() { ccm_activateFileManager(\'DASHBOARD\'); });</script>');
		$fileList = $this->getRequestedSearchResults();
		$files = $fileList->getPage();
				
		$this->set('fileList', $fileList);		
		$this->set('files', $files);		
		$this->set('pagination', $fileList->getPagination());
		

	}
	
	public function getRequestedSearchResults() {
		$fileList = new FileList();
		$keywords = htmlentities($_GET['fKeywords'], ENT_QUOTES, APP_CHARSET);
		$fileList->sortBy('fvDateAdded', 'desc');
		
		if ($keywords != '') {
			$fileList->filterByKeywords($keywords);
		}

		if ($_REQUEST['fNumResults']) {
			$fileList->setItemsPerPage($_REQUEST['fNumResults']);
		}
		if (isset($_GET['fSet']) && $_GET['fSet'] != '' && $_GET['fSet'] > 0) {
			Loader::model('file_set');
			$set = $_REQUEST['fSet'];
			$fs = FileSet::getByID($set);
			$fileList->filterBySet($fs);
		}

		if (isset($_GET['fType']) && $_GET['fType'] != '') {
			$type = $_REQUEST['fType'];
			$fileList->filterByType($type);
		}
		
		if (is_array($_REQUEST['fvSelectedField'])) {
			foreach($_REQUEST['fvSelectedField'] as $i => $item) {
				// due to the way the form is setup, index will always be one more than the arrays
				if ($item != '') {
					switch($item) {
						case "extension":
							$extension = $_REQUEST['extension'];
							$fileList->filterByExtension($extension);
							break;
						case "file_set":
							Loader::model('file_set');
							$set = $_REQUEST['file_set'];
							$fs = FileSet::getByID($set);
							$fileList->filterBySet($fs);
							break;
						case "type":
							$type = $_REQUEST['type'];
							$fileList->filterByType($type);
							break;
						case "date_added":
							$dateFrom = $_REQUEST['date_from'];
							$dateTo = $_REQUEST['date_to'];
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

						case "size":
							$from = $_REQUEST['size_from'];
							$to = $_REQUEST['size_to'];
							$fileList->filterBySize($from, $to);
							break;
						default:
							Loader::model('file_attributes');
							$akID = $item;
							$fak = FileAttributeKey::get($akID);
							switch($fak->getAttributeKeyType()) {
								case 'NUMBER':
									$numFrom = $_REQUEST['fakID_' . $akID . '_from'];
									$numTo = $_REQUEST['fakID_' . $akID . '_to'];
									if ($numFrom != '') {
										$fileList->filterByFileAttribute($fak->getAttributeKeyHandle(), $numFrom, '>=');
									}
									if ($numTo != '') {
										$fileList->filterByFileAttribute($fak->getAttributeKeyHandle(), $numTo, '<=');
									}
									break;
								case 'DATE':
									$dt = Loader::helper('form/date_time');
									$dateFrom = $dt->translate('fakID_' . $akID . '_from', $_REQUEST);
									$dateTo = $dt->translate('fakID_' . $akID . '_to', $_REQUEST);
									if ($dateFrom != '') {
										$fileList->filterByFileAttribute($fak->getAttributeKeyHandle(), $dateFrom, '>=');
									}
									if ($dateTo != '') {
										$fileList->filterByFileAttribute($fak->getAttributeKeyHandle(), $dateTo, '<=');
									}
									break;
								case 'BOOLEAN':
									$numFrom = $_REQUEST['fakID_' . $akID];
									$fileList->filterByFileAttribute($fak->getAttributeKeyHandle(), 1);
									break;
								case 'TEXT':
									$value = $_REQUEST['fakID_' . $akID];
									$fileList->filterByFileAttribute($fak->getAttributeKeyHandle(), $value);
									break;
								case 'SELECT':
									$value = $_REQUEST['fakID_' . $akID];
									$fileList->filterByFileAttribute($fak->getAttributeKeyHandle(), $value);
									break;
								case 'SELECT_MULTIPLE':
									$values = $_REQUEST['fakID_' . $akID];
									$fileList->filterByFileAttribute($fak->getAttributeKeyHandle(), $values);
									break;

							}						
					}
				}
			}
		}
		return $fileList;
	}
}

?>