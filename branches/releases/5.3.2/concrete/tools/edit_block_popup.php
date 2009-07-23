<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));  

if($_REQUEST['isGlobal'] && ($_REQUEST['btask']=='edit' || $_REQUEST['btask']=='template') ){
	$scrapbookHelper=Loader::helper('concrete/scrapbook'); 
	$c = $scrapbookHelper->getGlobalScrapbookPage();					
	$db = Loader::db();
	$arHandle=$db->getOne('SELECT arHandle FROM CollectionVersionBlocks WHERE bID=? AND cID=? AND isOriginal=1', array(intval($_REQUEST['bID']),intval($c->getCollectionId()))); 
	$a = Area::get( $c, $arHandle);				
	$b=Block::getByID( intval($_REQUEST['bID']), $c, $a);
	//redirect cID
	$rcID = intval($_REQUEST['cID']);
	$isGlobal=1;
}else{
	$c = Page::getByID($_REQUEST['cID']);
	$a = Area::get($c, $_REQUEST['arHandle']);
	$b = Block::getByID($_REQUEST['bID'], $c, $a);
}

$bp = new Permissions($b);
if (!$bp->canWrite()) {
	die(_("Access Denied."));
}

include(DIR_FILES_ELEMENTS_CORE . '/dialog_header.php');
$bv = new BlockView();
			
if($isGlobal){
	echo '<div class="ccm-notification">';
	echo t('This is a global block.  Editing it here will change all instances of this block throughout the site.');
	//echo t('This is a global block.  Edit it from the <a href="%s">Global Scrapbook</a> in your dashboard.<br /><br /><br />', View::url('/dashboard/scrapbook/') );
	//echo '[<a class="ccm-dialog-close">'.t('Close Window').'</a>]';
	echo '</div>';							
}  

if (is_object($b)) {
	switch($_REQUEST['btask']) { 
		case 'template': 		
			if ($bp->canWrite()) {
				$bv->renderElement('block_custom_template', array('b' => $b, 'rcID'=>$rcID));
			}
			break;
		case 'groups':
			if ($bp->canAdminBlock()) {
				$bv->renderElement('block_groups', array('b' => $b, 'rcID'=>$rcID));
			}
			break;
		case 'child_pages':
			if ($bp->canAdminBlock()) {
				$bv->renderElement('block_master_collection_alias', array('b' => $b));
			}
			break;
		case 'edit': 			
			if ($bp->canWrite()) {
				$bv->render($b, 'edit', array(
					'c' => $c,
					'a' => $a, 
					'rcID'=>$rcID
				));
			} 
			break;
	}
}

include(DIR_FILES_ELEMENTS_CORE . '/dialog_footer.php');