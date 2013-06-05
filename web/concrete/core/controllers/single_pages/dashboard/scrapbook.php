<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_Scrapbook extends Controller {

	public function view() {  
	
		//add required header libraries
		$html = Loader::helper('html');
		$scrapbookHelper=Loader::helper('concrete/scrapbook');
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
	
	public function delete_scrapbook($arHandle, $token){
		$valt = Loader::helper('validation/token');
		if(!$valt->validate('delete_scrapbook', $token)){
			$this->set('error', array($valt->getErrorMessage()));
			$this->view();
			return;
		}
		$db = Loader::db();
		$c = $this->getCollectionObject();
		$vals = array($arHandle, intval($c->getCollectionID()) );
		$db->query( 'DELETE FROM Areas WHERE arHandle=? AND cID=?', $vals);
		$db->query( 'DELETE FROM CollectionVersionBlocks WHERE arHandle=? AND cID=?', $vals);	
		Cache::flush(); 
		$this->redirect('/dashboard/scrapbook/');		
	} 
	
	public function addScrapbook(){
		$txt = Loader::helper('text');
		$valt = Loader::helper('validation/token');
		if(!$valt->validate('add_scrapbook')){
			$this->set('error', array($valt->getErrorMessage()));
			$this->view();
			return;
		}
		$scrapbookName = $txt->sanitize($_REQUEST['scrapbookName']); 
		$c=$this->getCollectionObject();
		$a = Area::get($c, $scrapbookName);
		if (!is_object($a)) {
			$a = Area::getOrCreate( $c, $scrapbookName);
		}		
		$this->redirect('/dashboard/scrapbook/');
	}	
	
	public function deleteBlock($name = '', $pcID = 0, $bID = 0, $token = ''){
		$valt = Loader::helper('validation/token');
		if(!$valt->validate('delete_scrapbook_block', $token)){
			$this->set('error', array($valt->getErrorMessage()));
			$this->view();
			return;
		}
		if($pcID > 0){
			$pc = PileContent::get($pcID);
			$p = $pc->getPile();
			if ($p->isMyPile()) {
				$pc->delete();
			}
		}else{
			$c = Page::getCurrentPage();
			$block=Block::getById($bID, $c, $name); 
			if( $block ){  //&& $block->getAreaHandle()=='Global Scrapbook'
				$block->delete(1);
			}
		}
		$this->view();
	}
	
	public function rename_block(){
		$valt = Loader::helper('validation/token');
		if(!$valt->validate('rename_scrapbook_block')){
			$this->set('error', array($valt->getErrorMessage()));
			$this->view();
			return;
		}
		$bID=intval($_REQUEST['bID']); 
		$globalScrapbookC=$this->getCollectionObject(); 
		$scrapbookName = $_REQUEST['scrapbookName']; 
		$globalScrapbookArea = Area::getOrCreate( $globalScrapbookC, $scrapbookName );
		$block=Block::getById($bID, $globalScrapbookC, $globalScrapbookArea); 		
		if($block && strlen($_POST['bName']) ){  //&& $block->getAreaHandle()=='Global Scrapbook'		
			//this is needed so the cache clears correctly
			$bp = new Permissions($block);
			if ($bp->canEditBlockPermissions()) { 
				$block->setBlockAreaObject($globalScrapbookArea);			
				$block->updateBlockName( $_POST['bName'], 1 );
			}
		} 
		header('Location: ' . View::url('/dashboard/scrapbook', 'view') . '?scrapbookName=' . $scrapbookName);
		exit;
	}
	
	public function rename_scrapbook(){
		$valt = Loader::helper('validation/token');
		if(!$valt->validate('rename_scrapbook')){
			$this->set('error', array($valt->getErrorMessage()));
			$this->view();
			return;
		}
		$txt = Loader::helper('text');
		$db = Loader::db();
		$c=$this->getCollectionObject();
		$scrapbookName=$txt->sanitize($_POST['scrapbookName']);
		
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