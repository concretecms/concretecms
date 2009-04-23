<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));

$c = Page::getByID($_REQUEST['cID']);
$a = Area::get($c, $_REQUEST['arHandle']);
$b = Block::getByID($_REQUEST['bID'], $c, $a);
$bp = new Permissions($b);
if (!$bp->canWrite()) {
	die(_("Access Denied."));
}

include(DIR_FILES_ELEMENTS_CORE . '/dialog_header.php');
$bv = new BlockView();

if (is_object($b)) {
	switch($_REQUEST['btask']) {
		case 'template':
			if ($bp->canAdminBlock()) {
				$bv->renderElement('block_custom_template', array('b' => $b));
			}
			break;
		case 'groups':
			if ($bp->canAdminBlock()) {
				$bv->renderElement('block_groups', array('b' => $b));
			}
			break;
		case 'child_pages':
			if ($bp->canAdminBlock()) {
				$bv->renderElement('block_master_collection_alias', array('b' => $b));
			}
			break;
		case 'edit':
			if($_REQUEST['isGlobal']){
				echo '<div style="text-align:center; margin:32px 0px">';
				echo t('This is a global block.  Edit it from the <a href="%s">Global Scrapbook</a> in your dashboard.<br /><br /><br />', View::url('/dashboard/scrapbook/global/') );
				echo '[<a class="ccm-dialog-close">'.t('Close Window').'</a>]';
				echo '</div>';
			}else{
				if ($bp->canWrite()) {
					$bv->render($b, 'edit', array(
						'c' => $c,
						'a' => $a
					));
				}
			}
			break;
	}
}

include(DIR_FILES_ELEMENTS_CORE . '/dialog_footer.php');