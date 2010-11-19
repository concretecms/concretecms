<?php 
defined('C5_EXECUTE') or die("Access Denied.");

Loader::model('pile');

class DashboardScrapbookController extends Controller {

	public function view() {  
	
		//add required header libraries
		$html = Loader::helper('html');
		$scrapbookHelper=Loader::helper('concrete/scrapbook');
		$this->addHeaderItem($html->css('ccm.filemanager.css'));
		$this->addHeaderItem($html->javascript('ccm.filemanager.js'));
		$this->addHeaderItem($html->javascript('tiny_mce/tiny_mce.js'));
		$this->addHeaderItem('<script type="text/javascript">$(function() { ccm_activateFileManager(\'DASHBOARD\'); });</script>');
		$c=$this->getCollectionObject();
		$cPath=$c->getCollectionPath();
		//echo $c->getCollectionId();
		
		/*
		$a = Area::get($c, t('Shared Scrapbook 1'));
		if (!is_object($a)) {
			$a = Area::getOrCreate($c, t('Shared Scrapbook 1'));
		}		
		*/
		//get available block areas	
		$availableScrapbooks = $scrapbookHelper->getAvailableScrapbooks();		
		$this->set('availableScrapbooks', $availableScrapbooks);
		
		$scrapbookName=$_REQUEST['scrapbookName'];
		//get scrapbook name from referrer if a block has just been added or edited
		if($_REQUEST['cID']==$c->getCollectionId() && $_REQUEST['mode']=='edit' && !$scrapbookName && stristr($_SERVER['HTTP_REFERER'],'scrapbookName=')){
			$startPos = strrpos($_SERVER['HTTP_REFERER'],'?')+1;
			$qStr = substr($_SERVER['HTTP_REFERER'],$startPos); 
			parse_str($qStr,$referrerVals);  
			$scrapbookName=$referrerVals['scrapbookName'];
			$this->redirect('/dashboard/scrapbook/?scrapbookName='.$scrapbookName);
		}
		
		//test that the requested scrapbook name is a valid one
		if($scrapbookName=='userScrapbook'){
			$validScrapbookName=1;
		}else{
			foreach($availableScrapbooks as $availableScrapbook){
				if($availableScrapbook['arHandle']==$scrapbookName)
					$validScrapbookName=1;
			}			
		}
		
		if( strlen($scrapbookName) && $validScrapbookName ){
			$this->set('scrapbookName', $scrapbookName);		
			$globalScrapbookArea = new Area( $scrapbookName );
			$globalScrapbookBlocks = $globalScrapbookArea->getAreaBlocksArray($c); 
			$this->set('globalScrapbookArea', $globalScrapbookArea);
			$this->set('globalScrapbookBlocks', $globalScrapbookBlocks);			
		}
		
		$this->set('availableScrapbooks', $availableScrapbooks);
		$this->set('cPath', $cPath); 
	}
	
	public function delete_scrapbook(){
		$db = Loader::db();
		$c = $this->getCollectionObject();
		$vals = array( $_REQUEST['arHandle'], intval($c->getCollectionID()) );
		$db->query( 'DELETE FROM Areas WHERE arHandle=? AND cID=?', $vals);
		$db->query( 'DELETE FROM CollectionVersionBlocks WHERE arHandle=? AND cID=?', $vals);	
		Cache::flush(); 
		$this->redirect('/dashboard/scrapbook/');		
	} 
	
	public function addScrapbook(){
		$scrapbookName = $_REQUEST['scrapbookName']; 
		$c=$this->getCollectionObject();
		$a = Area::get($c, $scrapbookName);
		if (!is_object($a)) {
			$a = Area::getOrCreate( $c, $scrapbookName);
		}		
		$this->redirect('/dashboard/scrapbook/');
	}	
	
	public function deleteBlock(){
		if( intval($_REQUEST['pcID']) ){
			$pc = PileContent::get($_REQUEST['pcID']);
			$p = $pc->getPile();
			if ($p->isMyPile()) {
				$pc->delete();
			}
		}else{
			$bID=intval($_REQUEST['bID']);
			$c = Page::getCurrentPage();
			$block=Block::getById($bID, $c, $_REQUEST['scrapbookName']); 
			if( $block ){  //&& $block->getAreaHandle()=='Global Scrapbook'
				$block->delete(1);
			}
		}
		$this->view();
	}
	
	public function rename_block(){
		$bID=intval($_REQUEST['bID']); 
		$globalScrapbookC=$this->getCollectionObject(); 
		$scrapbookName = $_REQUEST['scrapbookName']; 
		$globalScrapbookArea = Area::getOrCreate( $globalScrapbookC, $scrapbookName );
		$block=Block::getById($bID, $globalScrapbookC, $globalScrapbookArea); 		
		if($block && strlen($_POST['bName']) ){  //&& $block->getAreaHandle()=='Global Scrapbook'		
			//this is needed so the cache clears correctly
			$bp = new Permissions($block);
			if ($bp->canAdmin()) { 
				$block->setBlockAreaObject($globalScrapbookArea);			
				$block->updateBlockName( $_POST['bName'], 1 );
			}
		} 
		$this->view();	
	}
	
	public function rename_scrapbook(){
		$db = Loader::db();
		$c=$this->getCollectionObject();
		$scrapbookName=$_POST['scrapbookName'];
		
		//get original area name
		$vals=array(  intval($_POST['arID']), $c->getCollectionId() ); 
		$oldScrapbookName=$db->getOne( 'SELECT arHandle FROM Areas WHERE arID=? AND cID=?', $vals); 
		
		//update area name
		$vals=array( $scrapbookName, intval($_POST['arID']), $c->getCollectionId() );		
		$db->query( 'UPDATE Areas SET arHandle=? WHERE arID=? AND cID=?', $vals);
		
		//update area blocks
		if($oldScrapbookName){  
			$vals=array( $scrapbookName, $oldScrapbookName, $c->getCollectionId() );
			$db->query( 'UPDATE CollectionVersionBlocks SET arHandle=? WHERE arHandle=? AND cID=?', $vals);
		}
		
		$this->redirect('/dashboard/scrapbook/');
	}			
}

?>