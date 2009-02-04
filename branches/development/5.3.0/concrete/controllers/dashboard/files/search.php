<?
defined('C5_EXECUTE') or die(_("Access Denied."));
class DashboardFilesSearchController extends Controller {

	public function view() {
		$html = Loader::helper('html');
		$this->addHeaderItem($html->css('ccm.filemanager.css'));
		$this->addHeaderItem($html->javascript('ccm.filemanager.js'));
		$this->addHeaderItem('<script type="text/javascript">$(function() { ccm_activateFileManager(); });</script>');
	}

	
}

?>