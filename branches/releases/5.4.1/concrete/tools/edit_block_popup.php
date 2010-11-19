<?php 
defined('C5_EXECUTE') or die("Access Denied.");  

if($_REQUEST['isGlobal'] && ($_REQUEST['btask']=='edit' || $_REQUEST['btask'] == 'view_edit_mode' || $_REQUEST['btask']=='template') ){
	$scrapbookHelper=Loader::helper('concrete/scrapbook'); 
	$c = $scrapbookHelper->getGlobalScrapbookPage();					
	$db = Loader::db();
	$arHandle=$db->getOne('SELECT arHandle FROM CollectionVersionBlocks WHERE bID=? AND cID=? AND isOriginal=1', array(intval($_REQUEST['bID']),intval($c->getCollectionId()))); 
	$a = Area::get( $c, $arHandle);				
	$b=Block::getByID( intval($_REQUEST['bID']), $c, $a);
	//redirect cID
	$rcID = intval($_REQUEST['cID']);
	$isGlobal=1;
	$rarHandle = $_REQUEST['arHandle'];
}else{
	$c = Page::getByID($_REQUEST['cID']);
	$a = Area::get($c, $_REQUEST['arHandle']);
	$b = Block::getByID($_REQUEST['bID'], $c, $a);
}

$bp = new Permissions($b);
if (!$bp->canWrite()) {
	die(t("Access Denied."));
} 

if ($_REQUEST['btask'] != 'view' && $_REQUEST['btask'] != 'view_edit_mode') { 
	include(DIR_FILES_ELEMENTS_CORE . '/dialog_header.php');
}

$bv = new BlockView(); 
			
if($isGlobal && $_REQUEST['btask'] != 'view_edit_mode') {
	echo '<div class="ccm-notification">';
	echo t('This is a global block.  Editing it here will change all instances of this block throughout the site.');
	//echo t('This is a global block.  Edit it from the <a href="%s">Global Scrapbook</a> in your dashboard.<br /><br /><br />', View::url('/dashboard/scrapbook/') );
	//echo '[<a class="ccm-dialog-close">'.t('Close Window').'</a>]';
	echo '</div>';							
}  

if (is_object($b)) {
	switch($_REQUEST['btask']) {
		case 'block_css': 		
			if ($bp->canWrite()) {
				$style = $b->getBlockCustomStyleRule();
				$action = $b->getBlockUpdateCssAction();
				if ($_REQUEST['subtask'] == 'delete_custom_style_preset') {
					$styleToDelete = CustomStylePreset::getByID($_REQUEST['deleteCspID']);
					$styleToDelete->delete();
				}
				$refreshAction = REL_DIR_FILES_TOOLS_REQUIRED . '/edit_block_popup?btask=block_css&cID=' . $c->getCollectionID() . '&arHandle=' . $a->getAreaHandle() . '&bID=' . $b->getBlockID() . '&isGlobal=' . $_REQUEST['isGlobal'] . '&refresh=1';
				$bv->renderElement('custom_style', array('b' => $b, 'rcID'=>$rcID, 'c' => $c, 'a' => $a, 'style' => $style, 'action' => $action, 'refreshAction' => $refreshAction) );
			}
			break;	 
		case 'template': 		
			if ($bp->canWrite()) {
				$bv->renderElement('block_custom_template', array('b' => $b, 'rcID'=>$rcID));
			}
			break;
		case 'view':
			if ($bp->canRead()) {
				$bv->render($b, 'view', array(
					'c' => $c,
					'a' => $a
				));
			}
			break;
		case 'view_edit_mode':
			if ($bp->canWrite() || ($c->canWrite() && $b->isGlobalBlock() && $b->canRead())) {

				$btc = $b->getInstance();
				// now we inject any custom template CSS and JavaScript into the header
				if('Controller' != get_class($btc)){
					$btc->outputAutoHeaderItems();
				}
				$btc->runTask('on_page_view', array($bv));
				
				$v = View::getInstance();
				
				$items = $v->getHeaderItems();
				if (count($items) > 0) { ?>
				<script type="text/javascript">				
				<?php 
				foreach($items as $item) { 
					if ($item instanceof CSSOutputObject) { ?>
						// we only support CSS here
						ccm_addHeaderItem("<?php echo $item->href?>", 'CSS');
					<?php  } else if ($item instanceof JavaScriptOutputObject) { ?>
						ccm_addHeaderItem("<?php echo $item->href?>", 'JAVASCRIPT');
					<?php  }
				
				} ?>
				</script>
				<?php  }
				
				$bv->renderElement('block_controls', array(
					'a' => $a,
					'b' => $b,
					'p' => $bp
				));
				$bv->renderElement('block_header', array(
					'a' => $a,
					'b' => $b,
					'p' => $bp
				));
				$bv->render($b);
				$bv->renderElement('block_footer');
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
					'rcID'=>$rcID,
					'rarHandle' => $rarHandle
				));
			} 
			break;
	}
}

if ($_REQUEST['btask'] != 'view' && $_REQUEST['btask'] != 'view_edit_mode') { 
	include(DIR_FILES_ELEMENTS_CORE . '/dialog_footer.php');
}