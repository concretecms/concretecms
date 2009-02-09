<?
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('file_list');
Loader::model('file_set');
class DashboardFilesSearchController extends Controller {

	public function view() {
		$html = Loader::helper('html');
		$form = Loader::helper('form');
		$this->set('form', $form);
		$this->addHeaderItem($html->css('ccm.filemanager.css'));
		$this->addHeaderItem($html->javascript('ccm.filemanager.js'));
		$this->addHeaderItem('<script type="text/javascript">$(function() { ccm_activateFileManager(); });</script>');
		
		$ext1 = FileList::getExtensionList();
		$extensions = array();
		foreach($ext1 as $value) {
			$extensions[$value] = $value;
		}
		
		$t1 = FileList::getTypeList();
		$types = array();
		foreach($t1 as $value) {
			$types[$value] = FileType::getGenericTypeText($value);
		}
		
		$s1 = FileSet::getMySets();
		$sets = array();
		foreach($s1 as $s) {
			$sets[$s->getFileSetID()] = $s->getFileSetName();
		}
		
		$this->set('extensions', $extensions);		
		$this->set('types', $types);	
		$this->set('sets', $sets);	
		
		$fileList = $this->getRequestedSearchResults();
		$files = $fileList->getPage();
				
		$this->set('fileList', $fileList);		
		$this->set('files', $files);		
		$this->set('pagination', $fileList->getPagination());
	}
	
	public function getRequestedSearchResults() {
		$fileList = new FileList();
		$keywords = htmlentities($_GET['fKeywords']);
		
		if ($keywords != '') {
			$fileList->filterByKeywords($keywords);
		}
		
		if (is_array($_REQUEST['fvSelectedField'])) {
			foreach($_REQUEST['fvSelectedField'] as $index => $item) {
				if ($item != '') {
					switch($item) {
						case "extension":
							$extension = $_REQUEST['extension'][$index];
							$fileList->filterByExtension($extension);
							break;
						case "file_set":
							Loader::model('file_set');
							$set = $_REQUEST['file_set'][$index];
							$fs = FileSet::getByID($set);
							$fileList->filterBySet($fs);
							break;
						case "type":
							$type = $_REQUEST['type'][$index];
							$fileList->filterByType($type);
							break;
						case "date_added":
							$dateFrom = $_REQUEST['date_from'][$index];
							$dateTo = $_REQUEST['date_to'][$index];
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
							$from = $_REQUEST['size_from'][$index];
							$to = $_REQUEST['size_to'][$index];
							$fileList->filterBySize($from, $to);
							break;
						default:
							Loader::model('file_attributes');
							$handle = $_REQUEST['file_attribute_handle'][$index];
							$fak = FileAttributeKey::getByHandle($handle);
							print $fak->getAttributeKeyType();
							switch($fak->getAttributeKeyType()) {
								case 'NUMBER':
								$numFrom = $_REQUEST['fak_' . $handle . '_from'][$index];
								$numTo = $_REQUEST['fak_' . $handle . '_to'][$index];
								if ($numFrom != '') {
									$fileList->filterByFileAttribute($handle, $numFrom, '>=');
								}
								if ($numTo != '') {
									$fileList->filterByFileAttribute($handle, $numTo, '<=');
								}
							}
							break;
						
					}
				}
			}
		}
		return $fileList;
	}
}

?>