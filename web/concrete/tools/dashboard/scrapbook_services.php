<?
defined('C5_EXECUTE') or die("Access Denied.");

$scrapbookC = Page::getByPath("/dashboard/scrapbook");
$scrapbookPermissions = new Permissions($scrapbookC);
if (!$scrapbookPermissions->canRead()) {
	die(t("Access Denied."));
}
 
$db = Loader::db();

// update order of collections
Loader::model('user_attributes');

if($_REQUEST['mode']=='reorder'){
	if( is_array($_REQUEST['ccm-scrapbook-list-item']) ){ 
		$arHandle=$_REQUEST['arHandle'];
		$displayOrderCounter=0; 
		foreach( $_REQUEST['ccm-scrapbook-list-item'] as $bID ){
			if( intval($bID)==0 ) continue;
			$v = array( $displayOrderCounter, $scrapbookC->getCollectionId(), $bID, $arHandle);
			$db->Execute('update CollectionVersionBlocks set cbDisplayOrder = ? where cID = ? and bID = ? AND arHandle=?', $v);
			$displayOrderCounter++;
		}		
	}elseif( is_array($_REQUEST['ccm-pc']) ){ 
		$displayOrderCounter=0; 
		$u=new User();
		foreach( $_REQUEST['ccm-pc'] as $pcID ){
			if( intval($pcID)==0 ) continue;
			$v = array( $displayOrderCounter, $pcID, intval($u->uID) );
			$db->Execute('update PileContents AS pc LEFT JOIN Piles AS p ON p.pID=pc.pID set pc.displayOrder = ? where pc.pcID = ? AND p.uID=?', $v);
			$displayOrderCounter++;
		}	
	}	
	//$uats = $_REQUEST['item'];
	/*for($i = 0; $i < count($uats); $i++) {
		$uats[$i] = substr($uats[$i], 5);
	}*/
	//UserAttributeKey::updateAttributesDisplayOrder($uats);
}

?>