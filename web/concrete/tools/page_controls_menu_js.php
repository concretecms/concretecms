<?
defined('C5_EXECUTE') or die("Access Denied.");
header('Content-type: text/javascript');?>

var menuHTML = '';

<?
if ($_REQUEST['cvID'] > 0) {
	$c = Page::getByID($_REQUEST['cID'], $_REQUEST['cvID']);
} else {
	$c = Page::getByID($_REQUEST['cID']);
}
$cp = new Permissions($c);
$req = Request::get();
$req->setCurrentPage($c);

$valt = Loader::helper('validation/token');
$sh = Loader::helper('concrete/dashboard/sitemap');
$dh = Loader::helper('concrete/dashboard');
$ish = Loader::helper('concrete/interface');
$token = '&' . $valt->getParameter();

if (isset($cp)) {

	$u = new User();
	$username = $u->getUserName();
	$vo = $c->getVersionObject();

	$statusMessage = '';
	if ($c->isCheckedOut()) {
		if (!$c->isCheckedOutByMe()) {
			$cantCheckOut = true;
			$statusMessage .= t("%s is currently editing this page.", $c->getCollectionCheckedOutUserName());
		}
	}
	
	if ($c->getCollectionPointerID() > 0) {
		$statusMessage .= t("This page is an alias of one that actually appears elsewhere. ");
		$statusMessage .= "<br/><a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "'>" . t('View/Edit Original') . "</a>";
		if ($cp->canApprovePageVersions()) {
			$statusMessage .= "&nbsp;|&nbsp;";
			$statusMessage .= "<a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionPointerOriginalID() . "&ctask=remove-alias" . $token . "'>" . t('Remove Alias') . "</a>";
		}
	} else {
	
		if (is_object($vo)) {
			if (!$vo->isApproved() && !$c->isEditMode()) {
				$statusMessage .= t("This page is pending approval.");
				if ($cp->canApprovePageVersions() && !$c->isCheckedOut()) {
					$statusMessage .= "<br/><a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "&ctask=approve-recent" . $token . "'>" . t('Approve Version') . "</a>";
				}
			}
		}
		
		$pendingAction = $c->getPendingAction();
		if ($pendingAction == 'MOVE') {
			$statusMessage .= $statusMessage ? "&nbsp;|&nbsp;" : "";
			$statusMessage .= t("This page is being moved.");
			if ($cp->canApprovePageVersions() && (!$c->isCheckedOut() || ($c->isCheckedOut() && $c->isEditMode()))) {
				$statusMessage .= "<br/><a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "&ctask=approve_pending_action'>" . t('Approve Move') . "</a> | <a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "&ctask=clear_pending_action" . $token . "'>" . t('Cancel') . "</a>";
			}
		} else if ($pendingAction == 'DELETE') {
			$statusMessage .= $statusMessage ? "<br/>" : "";
			$statusMessage .= t("This page is marked for removal.");
			$children = $c->getNumChildren();
			if ($children > 0) {
				$pages = $children + 1;
				$statusMessage .= " " . t('This will remove %s pages.', $pages);
				if ($u->isSuperUser()) {
					$statusMessage .= " <a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "&ctask=approve_pending_action" . $token . "'>" . t('Approve Delete') . "</a> | <a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "&ctask=clear_pending_action" . $token . "'>" . t('Cancel') . "</a>";
				} else {
					$statusMessage .= " " . t('Only the super user can approve a multi-page delete operation.');
				}
			} else if ($children == 0 && $cp->canApprovePageVersions() && (!$c->isCheckedOut() || ($c->isCheckedOut() && $c->isEditMode()))) {
				$statusMessage .= " <a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "&ctask=approve_pending_action" . $token . "'>" . t('Approve Delete') . "</a> | <a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "&ctask=clear_pending_action" . $token . "'>" . t('Cancel') . "</a>";
			}
		}
	
	}

	if ($c->isMasterCollection()) {
		$statusMessage .= $statusMessage ? "<br/>" : "";
		$statusMessage .= t('Page Defaults for') . ' "' . $c->getCollectionTypeName() . '" ' . t("page type");
		$statusMessage .= "<br/>" . t('(All edits take effect immediately)');
	}

	if ($cp->canViewToolbar()) { 
	
		$cID = $c->getCollectionID(); ?>





menuHTML += '<div id="ccm-page-controls-wrapper" class="ccm-ui">';
menuHTML += '<div id="ccm-toolbar">';

menuHTML += '<ul id="ccm-main-nav">';
menuHTML += '<li id="ccm-logo-wrapper"><?=Loader::helper('concrete/interface')->getToolbarLogoSRC()?></li>';

<? if ($c->isMasterCollection()) { ?>
	menuHTML += '<li><a class="ccm-icon-back ccm-menu-icon" href="<?=View::url('/dashboard/pages/types')?>"><?=t('Page Types')?></a></li>';
<? } ?>

<?
	if ($cp->canViewToolbar()) {  ?>
	
	menuHTML += '<li <? if ($c->isEditMode()) { ?>class="ccm-nav-edit-mode-active"<? } ?>><a class="ccm-icon-edit ccm-menu-icon" id="ccm-nav-edit" href="<? if (!$c->isEditMode()) { ?><?=DIR_REL?>/<?=DISPATCHER_FILENAME?>?cID=<?=$c->getCollectionID()?>&ctask=check-out<?=$token?><? } else { ?>javascript:void(0);<? } ?>"><? if ($c->isEditMode()) { ?><?=t('Editing')?><? } else { ?><?=t('Edit')?></a><? } ?></li>';
	<?
	$items = $ihm->getPageHeaderMenuItems('left');
	foreach($items as $ih) {
		$cnt = $ih->getController(); 
		if ($cnt->displayItem()) {
		?>
			menuHTML += '<li><?=$cnt->getMenuLinkHTML()?></li>';
		<?
		}
	}
	
} ?>

<? if (Loader::helper('concrete/interface')->showWhiteLabelMessage()) { ?>
	menuHTML += '<li id="ccm-white-label-message"><?=t('Powered by <a href="%s">concrete5</a>.', CONCRETE5_ORG_URL)?></li>';
<? }
?>
menuHTML += '</ul>';
menuHTML += '<ul id="ccm-system-nav">';
<?
$items = $ihm->getPageHeaderMenuItems('right');
foreach($items as $ih) {
	$cnt = $ih->getController(); 
	if ($cnt->displayItem()) {
	?>
		menuHTML += '<li><?=$cnt->getMenuLinkHTML()?></li>';
	<?
	}
}
?>

<? if ($dh->canRead()) { ?>
	menuHTML += '<li><a class="ccm-icon-dashboard ccm-menu-icon" id="ccm-nav-dashboard" href="<?=View::url('/dashboard')?>"><?=t('Dashboard')?></a></li>';
<? } ?>
menuHTML += '<li id="ccm-nav-intelligent-search-wrapper"><input type="search" placeholder="<?=t('Intelligent Search')?>" id="ccm-nav-intelligent-search" tabindex="1" /></li>';
menuHTML += '<li><a id="ccm-nav-sign-out" class="ccm-icon-sign-out ccm-menu-icon" href="<?=View::url('/login', 'logout')?>"><?=t('Sign Out')?></a></li>';
menuHTML += '</ul>';

menuHTML += '</div>';

<?
$dh = Loader::helper('concrete/dashboard');
?>

menuHTML += '<?=addslashes($dh->getDashboardAndSearchMenus())?>';

menuHTML += '<div id="ccm-edit-overlay">';
menuHTML += '<div class="ccm-edit-overlay-inner">';

<? if ($c->isEditMode()) { ?>

menuHTML += '<div id="ccm-exit-edit-mode-direct" <? if ($vo->isNew()) { ?>style="display: none"<? } ?>>';
menuHTML += '<div class="ccm-edit-overlay-actions">';
menuHTML += '<a href="javascript:void(0)" onclick="window.location.href=\'<?=DIR_REL?>/<?=DISPATCHER_FILENAME?>?cID=<?=$c->getCollectionID()?>&ctask=check-in<?=$token?>\'" id="ccm-nav-exit-edit-direct" class="btn primary"><?=t('Exit Edit Mode')?></a>';
menuHTML += '</div>';
menuHTML += '<span class="label notice"><?=t('Version %s', $c->getVersionID())?></span>';
menuHTML += '<?=t('Page currently in edit mode on %s', date(DATE_APP_GENERIC_MDYT))?>';

menuHTML += '</div>';

menuHTML += '<div id="ccm-exit-edit-mode-comment" <? if (!$vo->isNew()) { ?>style="display: none"<? } ?>>';
menuHTML += '<div class="ccm-edit-overlay-actions" class="clearfix">';
menuHTML += '<form method="post" id="ccm-check-in" action="<?=DIR_REL?>/<?=DISPATCHER_FILENAME?>?cID=<?=$c->getCollectionID()?>&ctask=check-in">';
<? $valt = Loader::helper('validation/token'); ?>
menuHTML += '<?=$valt->output('', true)?>';
menuHTML += '<h4><?=t('Version Comments')?></h4>';
menuHTML += '<p><input type="text" name="comments" id="ccm-check-in-comments" value="<?=$vo->getVersionComments()?>" onclick="this.select()" style="width:520px"/></p>';
<? if ($cp->canApprovePageVersions()) { ?>
menuHTML += '<a href="javascript:void(0)" id="ccm-check-in-publish" class="btn primary" style="float: right"><span><?=t('Publish My Edits')?></span></a>';
<? } ?>
menuHTML += '<a href="javascript:void(0)" id="ccm-check-in-preview" class="btn" style="float: right"><span><?=t('Preview My Edits')?></span></a>';
menuHTML += '<a href="javascript:void(0)" id="ccm-check-in-discard" class="btn" style="float: left"><span><?=t('Discard My Edits')?></span></a>';
menuHTML += '<input type="hidden" name="approve" value="PREVIEW" id="ccm-approve-field" />';
menuHTML += '</form><br/>';

menuHTML += '</div>';
menuHTML += '<span class="label notice"><?=t('Version %s', $c->getVersionID())?></span>';
menuHTML += '<?=t('Page currently in edit mode on %s', date(DATE_APP_GENERIC_MDYT))?>';

menuHTML += '</div>';

<? } else { ?>

menuHTML += '<div class="ccm-edit-overlay-actions">';
<? if ($cp->canEditPageContents() || $cp->canEditPageContents()) { ?>
	menuHTML += '<a id="ccm-nav-check-out" href="<? if (!$cantCheckOut) { ?><?=DIR_REL?>/<?=DISPATCHER_FILENAME?>?cID=<?=$c->getCollectionID()?>&ctask=check-out<?=$token?><? } else { ?>javascript:void(0);<? } ?>" class="btn primary <? if ($cantCheckOut) { ?> disabled <? } ?> tooltip" <? if ($cantCheckOut) { ?>title="<?=t('Someone has already checked this page out for editing.')?>"<? } ?>><?=t('Edit this Page')?></a>';
<? } ?>
<? if ($cp->canAddSubpage()) { ?>
	menuHTML += '<a id="ccm-toolbar-add-subpage" dialog-width="645" dialog-modal="false" dialog-append-buttons="true" dialog-height="345" dialog-title="<?=t('Add a Sub-Page')?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup.php?cID=<?=$cID?>&ctask=add"class="btn"><?=t('Add a Sub-Page')?></a>';
<? } ?>
menuHTML += '</div>';
menuHTML += '<span class="label notice"><?=t('Version %s', $c->getVersionID())?></span>';
menuHTML += '<?=t('Page last edited on %s', $c->getCollectionDateLastModified(DATE_APP_GENERIC_MDYT))?>';


<? } ?>

menuHTML += '</div>';

<? if (!$cantCheckOut) { ?>

menuHTML += '<div id="ccm-edit-overlay-footer">';
menuHTML += '<div class="ccm-edit-overlay-inner">';
menuHTML += '<ul>';
<? if ($cp->canEditPageContents() || $cp->canEditPageContents()) { ?>
	menuHTML += '<li><a class="ccm-menu-icon ccm-icon-properties" id="ccm-toolbar-nav-properties" dialog-width="640" dialog-height="<? if ($cp->canApprovePageVersions() && (!$c->isEditMode())) { ?>450<? } else { ?>390<? } ?>" dialog-append-buttons="true" dialog-modal="false" dialog-title="<?=t('Page Properties')?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup.php?<? if ($cp->canApprovePageVersions() && (!$c->isEditMode())) { ?>approveImmediately=1<? } ?>&cID=<?=$c->getCollectionID()?>&ctask=edit_metadata"><?=t('Properties')?></a></li>';
<? } ?>
<? if ($cp->canEditPageDesign()) { ?>
	menuHTML += '<li><a class="ccm-menu-icon ccm-icon-design" id="ccm-toolbar-nav-design" dialog-append-buttons="true" dialog-width="610" dialog-height="405" dialog-modal="false" dialog-title="<?=t('Design')?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup.php?cID=<?=$cID?>&ctask=set_theme"><?=t('Design')?></a></li>';
<? } ?>
<? if ($cp->canEditPagePermissions()) { ?>
	menuHTML += '<li><a class="ccm-menu-icon ccm-icon-permissions" dialog-append-buttons="true" id="ccm-toolbar-nav-permissions" dialog-width="640" dialog-height="330" dialog-modal="false" dialog-title="<?=t('Permissions')?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup.php?&cID=<?=$cID?>&ctask=edit_permissions"><?=t('Permissions')?></a></li>';
<? } ?>
<? if ($cp->canViewPageVersions()) { ?>
	menuHTML += '<li><a class="ccm-menu-icon ccm-icon-versions" id="ccm-toolbar-nav-versions" dialog-width="640" dialog-height="340" dialog-modal="false" dialog-title="<?=t('Page Versions')?>" id="menuVersions<?=$cID?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/versions.php?cID=<?=$cID?>"><?=t('Versions')?></a></li>';
<? } ?>
<? if ($cp->canEditPageSpeedSettings()) { ?>
	menuHTML += '<li><a class="ccm-menu-icon ccm-icon-speed-settings" id="ccm-toolbar-nav-speed-settings" dialog-append-buttons="true" dialog-width="550" dialog-height="280" dialog-modal="false" dialog-title="<?=t('Speed Settings')?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup.php?&cID=<?=$cID?>&ctask=edit_speed_settings"><?=t('Speed Settings')?></a></li>';
<? } ?>
<?php  if ($sh->canRead()) { ?>
	menuHTML += '<li><a class="ccm-menu-icon ccm-icon-move-copy" id="ccm-toolbar-nav-move-copy" dialog-width="90%" dialog-height="70%" dialog-modal="false" dialog-title="<?=t('Move/Copy Page')?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/sitemap_search_selector?select_mode=move_copy_delete&cID=<?=$cID?>"><?=t('Move/Copy')?></a></li>';
<? } ?>
<? if ($cp->canDeletePage()) { ?>
	menuHTML += '<li><a class="ccm-menu-icon ccm-icon-delete" dialog-append-buttons="true" id="ccm-toolbar-nav-delete" dialog-width="360" dialog-height="150" dialog-modal="false" dialog-title="<?=t('Delete Page')?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup.php?&cID=<?=$cID?>&ctask=delete"><?=t('Delete')?></a></li>';
<? } ?>
menuHTML += '</ul>';
menuHTML += '</div>';
menuHTML += '</div>';

<? } ?>

menuHTML += '</div>';
menuHTML += '<?=addslashes($ish->getQuickNavigationBar())?>';

<?
/*

?>

menuHTML += '<li class="ccm-main-nav-arrange-option" <? if (!$c->isArrangeMode()) { ?> style="display: none" <? } ?>><a href="#" id="ccm-nav-save-arrange"><?=t('Save Positioning')?></a></li>';

menuHTML += '</ul>';
menuHTML += '</div>';
menuHTML += '<div id="ccm-page-detail"><div id="ccm-page-detail-l"><div id="ccm-page-detail-r" class="ccm-ui"><div id="ccm-page-detail-content"></div></div></div>';
menuHTML += '<div id="ccm-page-detail-lower"><div id="ccm-page-detail-bl"><div id="ccm-page-detail-br"><div id="ccm-page-detail-b"></div></div></div></div>';
menuHTML += '</div>';

<? */ ?>

<?
	}
	
} ?>

<?
if ($statusMessage != '') {?> 
	$(function() { ccmAlert.hud('<?=str_replace("'",'"',$statusMessage) ?>', 5000); });
<? } ?>

	
$(function() {
	<? if ($c->isEditMode()) { ?>
		$(ccm_editInit);	
	<? } ?>

	<? 
	if (!$dh->inDashboard()) { ?>
		$("#ccm-page-controls-wrapper").html(menuHTML);
		$(".tooltip").twipsy();
		ccm_activateToolbar();
	<? } ?>
	
	

	
});
