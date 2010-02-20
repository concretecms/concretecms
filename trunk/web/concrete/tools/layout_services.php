<?
defined('C5_EXECUTE') or die(_("Access Denied."));

$c = Page::getByID($_REQUEST['cID']);
$a = Area::get($c, $_GET['arHandle']);

$cp = new Permissions($c);
$ap = new Permissions($a);

$valt = Loader::helper('validation/token');
$token = '&' . $valt->getParameter();

 

Loader::model('layout');  
$layoutID = intval($_REQUEST['layoutID']);
$layout = Layout::getById($layoutID);

$jsonData = array('success'=>'0','msg'=>'', 'layoutID'=>$layoutID);

//ADD A CHECK TO MAKE SURE LAYOUT BELONGS TO AREA!!!!!!!!  

if ( !$cp->canWrite() || !$ap->canWrite()  ) {
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
			
		case 'delete': 
			$nvc = $c->getVersionToModify(); 
			$nvc->deleteAreaLayout( $a, $layout); 
			$jsonData['success'] = intval(1); 
			break;	
			
		case 'quicksave': 
			$breakPoints = explode('|',$_REQUEST['breakpoints']); 
			$cleanBreakPoints = array();
			foreach($breakPoints as $breakPoint){
				$cleanBreakPoints[]= floatval(str_replace('%','',$breakPoint)).'%';
			} 
			$layout->breakpoints = $cleanBreakPoints;
			if( count($layout->breakpoints) != ($layout->columns-1) )
				 $jsonData['msg']=t('Error: Invalid column count. Please refresh your page.'); 
			else{
				$nvc = $c->getVersionToModify(); 
				if( !$layout->isUniqueToCollectionVersion($nvc) ){
					$oldLayoutId=$layout->layoutID;
					$layout->layoutID=0;
				}
				$saved = $layout->save();
				if($oldLayoutId) $nvc->updateAreaLayoutId($a, $oldLayoutId, $layout->layoutID ); 
				
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
echo $json->encode( $jsonData );
?>