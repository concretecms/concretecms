<?
defined('C5_EXECUTE') or die("Access Denied.");

$c = Page::getByID($_REQUEST['cID']);
$a = Area::get($c, $_GET['arHandle']);

$nvc = $c->getVersionToModify(); 

$cp = new Permissions($c);
$ap = new Permissions($a);

$valt = Loader::helper('validation/token');
$token = '&' . $valt->getParameter();

if($_GET['task']=='deletePreset'){
	$layoutPreset = LayoutPreset::getByID($_REQUEST['lpID']);
	if(is_object($layoutPreset)){
		$layout = $layoutPreset->getLayoutObject(); 
		$layoutID = $layout->layoutID;
	}
}else{
	$layoutID = intval($_REQUEST['layoutID']); 
	$layout = Layout::getById($layoutID);
	$layoutPreset = $layoutPreset = $layout->getLayoutPresetObj();
} 

$jsonData = array('success'=>'0','msg'=>'', 'layoutID'=>intval($layoutID));

//security checks: make sure this layout belongs to this area & collection
if ( is_object($layout) && is_object($a) && is_object($c) ){  
	$db = Loader::db();
	$vals = array( $layoutID, $a->getAreaHandle(), intval($nvc->cID), intval($nvc->getVersionID())  ); 
	$areaLayoutData = $db->getRow('SELECT * FROM CollectionVersionAreaLayouts WHERE layoutID=? AND arHandle=? AND cID=? AND cvID=?',$vals);
	$layout->setAreaNameNumber( $areaLayoutData['areaNameNumber'] );
	$validLayout = (intval($areaLayoutData['layoutID'])>0 || is_object($layoutPreset)) ? true : false; 
	if($validLayout) $cvalID = intval($areaLayoutData['cvalID']);
}

if ( !$validLayout || !$cp->canEditPageContents() || !$ap->canAddLayoutToArea()  ) {
	$jsonData['msg']=t('Access Denied.'); 
	
}elseif ( !is_object($layout) ) {
	$jsonData['msg']=t('Error: Layout not found'); 
	
}else{ 
	
	switch($_GET['task']) {
		
		case 'lock':
			$layout->locked = (intval($_REQUEST['lock']))?1:0;
			$saved = $layout->save();
			$jsonData['success'] = intval($saved); 
			break;
			
		case 'move': 
			$cvalID=intval($_REQUEST['cvalID']);
			$layout = Layout::getByID($_REQUEST['layoutID']);
			$db = Loader::db();
			$layouts = $a->getAreaLayouts($nvc);
			$direction = $_REQUEST['direction']; 
			for($i=0; $i<count($layouts); $i++){  
				$layout=$layouts[$i]; 
				if($layout->getLayoutID()==$_REQUEST['layoutID'] ){
					if( $direction=='up' && $i>0 ){
						$prevLayout=$layouts[$i-1];
						$layout->position = $prevLayout->position;
						$prevLayout->position = $prevLayout->position+1;
						$vals = array( $prevLayout->position, intval($prevLayout->cvalID) );
						$sql = 'UPDATE CollectionVersionAreaLayouts SET position=? WHERE cvalID=? ';  
						$db->query($sql,$vals);
						$siblingMoved=1;
					}elseif($direction=='down' && ($i+1)<count($layouts)){
						$nextLayout=$layouts[$i+1];
						$layout->position = $nextLayout->position;
						$nextLayout->position = $nextLayout->position-1; 
						$vals = array( $nextLayout->position, intval($nextLayout->cvalID) );
						$sql = 'UPDATE CollectionVersionAreaLayouts SET position=? WHERE cvalID=? ';  
						$db->query($sql,$vals); 
						$siblingMoved=1;
					} 
					if($siblingMoved==1){
						$sql = 'UPDATE CollectionVersionAreaLayouts SET position=? WHERE cvalID=? ';  
						$db->query($sql, array( $layout->position, $layout->cvalID ));
										
					} 
					break;
				} 
			} 
			$jsonData['success'] = 1; 
			break;	 
			
		case 'deleteOpts':
			Loader::element('block_area_layout_delete_opts', array('cvalID'=>$layout->cvalID));
			die;
			break;

		case 'delete':  
			$nvc->deleteAreaLayout( $a, $layout, intval($_REQUEST['deleteBlocks'])); 
			$jsonData['refreshPage'] = (intval($_REQUEST['deleteBlocks']))?0:1;  
			$jsonData['success'] = 1; 
			break;	
			
		case 'deletePreset':
			if(is_object($layoutPreset)) $layoutPreset->delete(); 
			$jsonData['success'] = 1; 
			break;
			
		case 'quicksave': 
			$breakPoints = explode('|',$_REQUEST['breakpoints']); 
			$cleanBreakPoints = array();
			foreach($breakPoints as $breakPoint){
				$cleanBreakPoints[]= floatval(str_replace('%','',$breakPoint)).'%';
			} 
			$layout->breakpoints = $cleanBreakPoints; 
			
			if( count($layout->breakpoints) != ($layout->columns-1) ){
				 $jsonData['msg']=t('Error: Invalid column count. Please refresh your page.'); 
			}else{ 
			
				if( !$layout->isUniqueToCollectionVersion($nvc) && !$layoutPreset ){
					$oldLayoutId=$layout->layoutID;
					$oldLayout_cvalID=$layout->cvalID;
					$layout->layoutID=0;
				}  
				
				$saved = $layout->save();  
				if( $oldLayoutId && !$layoutPreset ) $nvc->updateAreaLayoutId( intval($cvalID), $layout->layoutID ); 

				$jsonData['layoutID'] = $layout->getLayoutID(); 
				$jsonData['success'] = intval($saved); 
			} 			
			
			break;				
			
		default:
			$jsonData['msg']=t('Invalid Task.'); 
			break;
			
	} 
	
}

if( !$jsonData['msg'] && !intval($jsonData['success']) ) $jsonData['msg']=t('Unknown Error'); 
if( !$jsonData['msg'] && intval($jsonData['success']) ) $jsonData['msg']=t('Success');

$json = Loader::helper('json');
if( $_GET['task']=='deleteOpts') echo $jsonData['msg'];
else echo $json->encode( $jsonData );
?>