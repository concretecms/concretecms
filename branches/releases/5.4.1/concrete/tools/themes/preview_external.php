<?php 
defined('C5_EXECUTE') or die("Access Denied.");

Loader::model('collection_types');

$previewCID=intval($_REQUEST['previewCID']);
$themeCID=intval($_REQUEST['themeCID']);
$themeHandle=$_REQUEST['themeHandle'];
$ctID=intval($_REQUEST['ctID']);
$collectionType=CollectionType::getByID($ctID);
if($collectionType) $ctHandle=$collectionType->getCollectionTypeHandle();

$c = Page::getByID($previewCID,"RECENT");
$cp = new Permissions($c);
if(!$cp->canWrite()) throw new Exception(_('Access Denied.'));

//$previewVersion=$previewCollection->getVersionObject();
$previewVersionID=$c->getVersionID();

$db=Loader::db();
$previewBlocksData=$db->getAll('SELECT bID, arHandle FROM CollectionVersionBlocks AS cvb WHERE cvID=? AND cID=?',array($previewVersionID, $previewCID) );
$areasBlocksHTML=array();
//get collection areas / blocks HTML
foreach($previewBlocksData as $previewBlockData){
	if( !intval($previewBlockData['bID']) || !strlen($previewBlockData['arHandle']) ) continue; 
	$b = Block::getByID( intval($previewBlockData['bID']) ); 
	$bv = new BlockView();
	ob_start();
    $bv->render($b);
	$blockHTML=ob_get_contents();
	ob_end_clean();	
	$areasBlocksHTML[$previewBlockData['arHandle']][]= $blockHTML;
}

$areasBlocksSerialized=serialize($areasBlocksHTML);
$postStr='content='.urlencode($areasBlocksSerialized).'&themeHandle='.$themeHandle.'&ctID='.$ctID.'&ctHandle='.$ctHandle;

if (!function_exists('curl_init')) { ?>
	<div><?php echo t('curl must be enabled to preview external themes.')?></div>
<?php  }else{
	$curl_handle = curl_init();
	curl_setopt($curl_handle, CURLOPT_URL, MARKETPLACE_THEME_PREVIEW_URL);
	curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $postStr);
	//curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
	$contents = curl_exec($curl_handle);
	curl_close($curl_handle);
	echo $contents;
} 
/*
foreach($areasBlocksHTML as $areaName=>$areaBlocksHTML){
	echo '<br><br><strong>'.$areaName.'</strong>';
	foreach($areaBlocksHTML as $areaBlockHTML){
		echo $areaBlockHTML.'<br>';
	}
}
*/
?>