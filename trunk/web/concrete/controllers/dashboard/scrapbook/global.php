<?
defined('C5_EXECUTE') or die(_("Access Denied."));

class DashboardScrapbookGlobalController extends Controller {

	public function view() { 
		$html = Loader::helper('html');
		$this->addHeaderItem($html->css('ccm.filemanager.css'));
		$this->addHeaderItem($html->javascript('ccm.filemanager.js'));
		$this->addHeaderItem($html->javascript('tiny_mce_309/tiny_mce.js'));
		$this->addHeaderItem('<script type="text/javascript">$(function() { ccm_activateFileManager(\'DASHBOARD\'); });</script>');
	}
	
	public function delete(){
		$bID=intval($_REQUEST['bID']);
		$block=Block::getById($bID);  
		if($block){  //&& $block->getAreaHandle()=='Global Scrapbook'
			$block->delete(1);
		}
		$this->view();
	}
	
	public function rename_block(){
		$bID=intval($_REQUEST['bID']);
		$globalScrapbookC = Page::getByPath('/dashboard/scrapbook/global'); 
		$globalScrapbookArea = new Area('Global Scrapbook');
		$block=Block::getById($bID, $globalScrapbookC, $globalScrapbookArea); 		
		if($block && strlen($_POST['bName']) ){  //&& $block->getAreaHandle()=='Global Scrapbook'		
			//this is needed so the cache clears correctly
			$block->setBlockAreaObject($globalScrapbookArea);
			
			$block->updateBlockName( $_POST['bName'], 1 );
		} 
		$this->view();	
	}
	
}

?>