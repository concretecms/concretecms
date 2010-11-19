<?php 
defined('C5_EXECUTE') or die("Access Denied.");
header('Content-type: text/javascript');?>

var menuHTML = '';

<?php 
if ($_REQUEST['cvID'] > 0) {
	$c = Page::getByID($_REQUEST['cID'], $_REQUEST['cvID']);
} else {
	$c = Page::getByID($_REQUEST['cID']);
}
$cp = new Permissions($c);

$valt = Loader::helper('validation/token');
$sh = Loader::helper('concrete/dashboard/sitemap');
$dh = Loader::helper('concrete/dashboard');
$token = '&' . $valt->getParameter();

if (isset($cp)) {

	$u = new User();
	$username = $u->getUserName();
	$vo = $c->getVersionObject();

	$statusMessage = '';
	if ($c->isCheckedOut()) {
		if (!$c->isCheckedOutByMe()) {
			$cantCheckOut = true;
			$statusMessage .= t("Another user is currently editing this page.");
		}
	}
	
	if ($c->getCollectionPointerID() > 0) {
		$statusMessage .= t("This page is an alias of one that actually appears elsewhere. ");
		$statusMessage .= "<br/><a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "'>" . t('View/Edit Original') . "</a>";
		if ($cp->canApproveCollection()) {
			$statusMessage .= "&nbsp;|&nbsp;";
			$statusMessage .= "<a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionPointerOriginalID() . "&ctask=remove-alias" . $token . "'>" . t('Remove Alias') . "</a>";
		}
	} else {
	
		if (is_object($vo)) {
			if (!$vo->isApproved() && !$c->isEditMode()) {
				$statusMessage .= t("This page is pending approval.");
				if ($cp->canApproveCollection() && !$c->isCheckedOut()) {
					$statusMessage .= "<br/><a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "&ctask=approve-recent" . $token . "'>" . t('Approve Version') . "</a>";
				}
			}
		}
		
		$pendingAction = $c->getPendingAction();
		if ($pendingAction == 'MOVE') {
			$statusMessage .= $statusMessage ? "&nbsp;|&nbsp;" : "";
			$statusMessage .= t("This page is being moved.");
			if ($cp->canApproveCollection() && (!$c->isCheckedOut() || ($c->isCheckedOut() && $c->isEditMode()))) {
				$statusMessage .= "<br/><a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "&ctask=approve_pending_action'>" . t('Approve Move') . "</a> | <a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "&ctask=clear_pending_action" . $token . "'>" . t('Cancel') . "</a>";
			}
		} else if ($pendingAction == 'DELETE') {
			$statusMessage .= $statusMessage ? "<br/>" : "";
			$statusMessage .= t("This page is marked for removal.");
			$children = $c->getNumChildren();
			if ($children > 0) {
				$pages = $children + 1;
				$statusMessage .= " " . t('This will remove %s pages.', $pages);
				if ($cp->canAdminPage()) {
					$statusMessage .= " <a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "&ctask=approve_pending_action" . $token . "'>" . t('Approve Delete') . "</a> | <a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "&ctask=clear_pending_action" . $token . "'>" . t('Cancel') . "</a>";
				} else {
					$statusMessage .= " " . t('Only administrators can approve a multi-page delete operation.');
				}
			} else if ($children == 0 && $cp->canApproveCollection() && (!$c->isCheckedOut() || ($c->isCheckedOut() && $c->isEditMode()))) {
				$statusMessage .= " <a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "&ctask=approve_pending_action" . $token . "'>" . t('Approve Delete') . "</a> | <a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "&ctask=clear_pending_action" . $token . "'>" . t('Cancel') . "</a>";
			}
		}
	
	}

	if ($cp->canWrite() || $cp->canAddSubContent() || $cp->canAdminPage() || $cp->canApproveCollection()) { ?>

menuHTML += '<div id="ccm-page-controls">';
menuHTML += '<div id="ccm-logo-wrapper"><img src="<?php echo ASSETS_URL_IMAGES?>/logo_menu.png" width="49" height="49" id="ccm-logo" /></div>';
menuHTML += '<div id="ccm-system-nav-wrapper1">';
menuHTML += '<div id="ccm-system-nav-wrapper2">';
menuHTML += '<ul id="ccm-system-nav">';

<?php  
if ($sh->canRead() && $u->config('UI_SITEMAP')) { ?>
	menuHTML += '<li><a id="ccm-nav-sitemap" dialog-title="<?php echo t('Navigate to Page')?>" href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/sitemap_search_selector?callback=ccm_goToSitemapNode&sitemap_select_mode=select_page" dialog-on-open="$(\'#ccm-nav-sitemap\').removeClass(\'ccm-nav-loading\')" dialog-width="90%" dialog-height="70%" dialog-modal="false"><?php echo t('Sitemap')?></a></li>';
<?php 
}
$fp = FilePermissions::getGlobal();
if ($fp->canSearchFiles() && $u->config('UI_FILEMANAGER')) { ?>
	menuHTML += '<li><a id="ccm-nav-file-manager" dialog-title="<?php echo t('File Manager')?>" href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/files/search_dialog?disable_choose=1" dialog-on-open="$(\'#ccm-nav-file-manager\').removeClass(\'ccm-nav-loading\')" dialog-width="90%" dialog-height="70%" dialog-modal="false"><?php echo t('File Manager')?></a></li>';

<?php 
}
?>

<?php  if ($dh->canRead()) { ?>
	menuHTML += '<li><a id="ccm-nav-dashboard" href="<?php echo View::url('/dashboard')?>"><?php echo t('Dashboard')?></a></li>';
<?php  } ?>
menuHTML += '<li><a id="ccm-nav-help" dialog-title="<?php echo t('Help')?>" dialog-on-open="$(\'#ccm-nav-help\').removeClass(\'ccm-nav-loading\')" href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/help/" dialog-width="500" dialog-height="350" dialog-modal="false"><?php echo t('Help')?></a></li>';
menuHTML += '<li class="ccm-last"><a id="ccm-nav-logout" href="<?php echo View::url('/login', 'logout')?>"><?php echo t('Sign Out')?></a></li>';
menuHTML += '</ul>';
menuHTML += '</div>';
menuHTML += '</div>';

menuHTML += '<ul id="ccm-main-nav">';
<?php  if (!$c->isAlias()) { ?>
menuHTML += '<li class="ccm-main-nav-view-option" <?php  if ($c->isEditMode()) { ?> style="display: none" <?php  } ?>><?php  if ($cantCheckOut) { ?><span id="ccm-nav-edit"><?php echo t('Edit Page')?></span><?php  } else if ($cp->canWrite() || $cp->canApproveCollection()) { ?><a href="javascript:void(0)" id="ccm-nav-edit"><?php echo t('Edit Page')?></a><?php  } ?></li>';
<?php  if ($cp->canAddSubContent()) { ?>
	menuHTML += '<li class="ccm-main-nav-view-option" <?php  if ($c->isEditMode()) { ?> style="display: none" <?php  } ?>><a href="javascript:void(0)" id="ccm-nav-add"><?php echo t('Add Page')?></a></li>';
<?php  } ?>
menuHTML += '<li class="ccm-main-nav-arrange-option" <?php  if (!$c->isArrangeMode()) { ?> style="display: none" <?php  } ?>><a href="#" id="ccm-nav-save-arrange"><?php echo t('Save Positioning')?></a></li>';
menuHTML += '<li class="ccm-main-nav-edit-option ccm-main-nav-exit-edit-mode-direct" <?php  if (!$c->isEditMode() || ($vo->isNew()))  { ?> style="display: none" <?php  } ?>><a href="<?php echo DIR_REL?>/<?php echo DISPATCHER_FILENAME?>?cID=<?php echo $c->getCollectionID()?>&ctask=check-in<?php echo $token?>" id="ccm-nav-exit-edit-direct"><?php echo t('Exit Edit Mode')?></a></li>';
menuHTML += '<li class="ccm-main-nav-edit-option ccm-main-nav-exit-edit-mode" <?php  if (!$c->isEditMode() || (!$vo->isNew())) { ?> style="display: none" <?php  } ?>><a href="javascript:void(0)" id="ccm-nav-exit-edit"><?php echo t('Exit Edit Mode')?></a></li>';
<?php  if ($cp->canWrite()) { ?>
	menuHTML += '<li class="ccm-main-nav-edit-option" <?php  if (!$c->isEditMode()) { ?> style="display: none" <?php  } ?>><a href="javascript:void(0)" id="ccm-nav-properties"><?php echo t('Properties')?></a></li>';
<?php  } ?>
<?php  if ($cp->canAdminPage()) { ?>
menuHTML += '<li class="ccm-main-nav-edit-option" <?php  if (!$c->isEditMode()) { ?> style="display: none" <?php  } ?>><a href="javascript:void(0)" id="ccm-nav-design"><?php echo t('Design')?></a></li>';
menuHTML += '<li class="ccm-main-nav-edit-option" <?php  if (!$c->isEditMode()) { ?> style="display: none" <?php  } ?>><a href="javascript:void(0)" id="ccm-nav-permissions"><?php echo t('Permissions')?></a></li>';
<?php  } ?>
<?php  if ($cp->canReadVersions()) { ?>
	menuHTML += '<li class="ccm-main-nav-edit-option" <?php  if (!$c->isEditMode()) { ?> style="display: none" <?php  } ?>><a href="javascript:void(0)" id="ccm-nav-versions"><?php echo t('Versions')?></a></li>';
<?php  } ?>
<?php  if ($sh->canRead() || $cp->canDeleteCollection()) { ?>
	menuHTML += '<li class="ccm-main-nav-edit-option" <?php  if (!$c->isEditMode()) { ?> style="display: none" <?php  } ?>><a href="javascript:void(0)" id="ccm-nav-mcd"><?php echo t('Move/Delete')?></a></li>';
<?php  } ?>
<?php  } ?>
menuHTML += '</ul>';
menuHTML += '</div>';
menuHTML += '<div id="ccm-page-detail"><div id="ccm-page-detail-l"><div id="ccm-page-detail-r"><div id="ccm-page-detail-content"></div></div></div>';
menuHTML += '<div id="ccm-page-detail-lower"><div id="ccm-page-detail-bl"><div id="ccm-page-detail-br"><div id="ccm-page-detail-b"></div></div></div></div>';
menuHTML += '</div>';

<?php  if ($c->getCollectionParentID() > 0) { ?>
	menuHTML += '<div id="ccm-bc">';
	menuHTML += '<div id="ccm-bc-inner">';
	<?php 
		$nh = Loader::helper('navigation');
		$trail = $nh->getTrailToCollection($c);
		$trail = array_reverse($trail);
		$trail[] = $c;
	?>
	menuHTML += '<ul>';
	<?php  foreach($trail as $_c) { ?>
		menuHTML += '<li><a href="#" onclick="javascript:location.href=\'<?php echo $nh->getLinkToCollection($_c)?>\'"><?php echo htmlentities($_c->getCollectionName(), ENT_QUOTES, APP_CHARSET)?></a></li>';
	<?php  } ?>
	menuHTML += '</ul>';
	
	menuHTML += '</div>';
	menuHTML += '</div>';
<?php  } ?>

<?php 
if ($statusMessage != '') {?> 
	$(function() { ccmAlert.hud('<?php echo str_replace("'",'"',$statusMessage) ?>', 5000); });
<?php  } ?>

	
	$(function() {
		$(document.body).prepend('<div id="ccm-page-controls-wrapper"></div>');
		$("#ccm-page-controls-wrapper").html(menuHTML);
	
		<?php  if ($c->isArrangeMode()) { ?>
			$(ccm_arrangeInit);	
		<?php  } else if ($c->isEditMode()) { ?>
			$(ccm_editInit);	
		<?php  } else { ?>
			$(ccm_init);
		<?php  } ?>
		
		<?php  if ($u->config('UI_BREADCRUMB')) {  ?>
			ccm_setupBreadcrumb();
		<?php  } ?>
		
	});
<?php 
	}
	
} ?>

