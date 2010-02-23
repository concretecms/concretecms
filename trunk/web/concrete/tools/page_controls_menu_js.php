<?
defined('C5_EXECUTE') or die(_("Access Denied."));
header('Content-type: text/javascript');?>

var menuHTML = '';

<?
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
		$statusMessage .= "<br/><a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "&ctask=approve-recent'>" . t('View/Edit Original') . "</a>";
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

	if ($cp->canWrite() || $cp->canAddSubContent() || $cp->canAdminPage()) { ?>

menuHTML += '<div id="ccm-page-controls">';
menuHTML += '<div id="ccm-logo-wrapper"><img src="<?=ASSETS_URL_IMAGES?>/logo_menu.png" width="49" height="49" id="ccm-logo" /></div>';
menuHTML += '<div id="ccm-system-nav-wrapper1">';
menuHTML += '<div id="ccm-system-nav-wrapper2">';
menuHTML += '<ul id="ccm-system-nav">';
<? if ($dh->canRead()) { ?>
	menuHTML += '<li><a id="ccm-nav-dashboard" href="<?=View::url('/dashboard')?>"><?=t('Dashboard')?></a></li>';
<? } ?>
menuHTML += '<li><a id="ccm-nav-help" dialog-title="<?=t('Help')?>" dialog-on-open="$(\'#ccm-nav-help\').removeClass(\'ccm-nav-loading\')" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/help/" dialog-width="500" dialog-height="350" dialog-modal="false"><?=t('Help')?></a></li>';
menuHTML += '<li class="ccm-last"><a id="ccm-nav-logout" href="<?=View::url('/login', 'logout')?>"><?=t('Sign Out')?></a></li>';
menuHTML += '</ul>';
menuHTML += '</div>';
menuHTML += '</div>';

menuHTML += '<ul id="ccm-main-nav">';
menuHTML += '<li class="ccm-main-nav-view-option" <? if ($c->isEditMode()) { ?> style="display: none" <? } ?>><? if ($cantCheckOut) { ?><span id="ccm-nav-edit"><?=t('Edit Page')?></span><? } else if ($cp->canWrite()) { ?><a href="javascript:void(0)" id="ccm-nav-edit"><?=t('Edit Page')?></a><? } ?></li>';
<? if ($cp->canAddSubContent()) { ?>
	menuHTML += '<li class="ccm-main-nav-view-option" <? if ($c->isEditMode()) { ?> style="display: none" <? } ?>><a href="javascript:void(0)" id="ccm-nav-add"><?=t('Add Page')?></a></li>';
<? } ?>
menuHTML += '<li class="ccm-main-nav-arrange-option" <? if (!$c->isArrangeMode()) { ?> style="display: none" <? } ?>><a href="#" id="ccm-nav-save-arrange"><?=t('Save Positioning')?></a></li>';
menuHTML += '<li class="ccm-main-nav-edit-option ccm-main-nav-exit-edit-mode-direct" <? if (!$c->isEditMode() || ($vo->isNew()))  { ?> style="display: none" <? } ?>><a href="<?=DIR_REL?>/index.php?cID=<?=$c->getCollectionID()?>&ctask=check-in<?=$token?>" id="ccm-nav-exit-edit-direct"><?=t('Exit Edit Mode')?></a></li>';
menuHTML += '<li class="ccm-main-nav-edit-option ccm-main-nav-exit-edit-mode" <? if (!$c->isEditMode() || (!$vo->isNew())) { ?> style="display: none" <? } ?>><a href="javascript:void(0)" id="ccm-nav-exit-edit"><?=t('Exit Edit Mode')?></a></li>';
menuHTML += '<li class="ccm-main-nav-edit-option" <? if (!$c->isEditMode()) { ?> style="display: none" <? } ?>><a href="javascript:void(0)" id="ccm-nav-properties"><?=t('Properties')?></a></li>';
<? if ($cp->canAdminPage()) { ?>
menuHTML += '<li class="ccm-main-nav-edit-option" <? if (!$c->isEditMode()) { ?> style="display: none" <? } ?>><a href="javascript:void(0)" id="ccm-nav-design"><?=t('Design')?></a></li>';
menuHTML += '<li class="ccm-main-nav-edit-option" <? if (!$c->isEditMode()) { ?> style="display: none" <? } ?>><a href="javascript:void(0)" id="ccm-nav-permissions"><?=t('Permissions')?></a></li>';
<? } ?>
<? if ($cp->canReadVersions()) { ?>
	menuHTML += '<li class="ccm-main-nav-edit-option" <? if (!$c->isEditMode()) { ?> style="display: none" <? } ?>><a href="javascript:void(0)" id="ccm-nav-versions"><?=t('Versions')?></a></li>';
<? } ?>
<? if ($sh->canRead() || $cp->canDeleteCollection()) { ?>
	menuHTML += '<li class="ccm-main-nav-edit-option" <? if (!$c->isEditMode()) { ?> style="display: none" <? } ?>><a href="javascript:void(0)" id="ccm-nav-mcd"><?=t('Move/Delete')?></a></li>';
<? } ?>
menuHTML += '</ul>';
menuHTML += '</div>';
menuHTML += '<div id="ccm-page-detail"><div id="ccm-page-detail-l"><div id="ccm-page-detail-r"><div id="ccm-page-detail-content"></div></div></div>';
menuHTML += '<div id="ccm-page-detail-lower"><div id="ccm-page-detail-bl"><div id="ccm-page-detail-br"><div id="ccm-page-detail-b"></div></div></div></div>';
menuHTML += '</div>';

<? if ($c->getCollectionParentID() > 0) { ?>
	menuHTML += '<div id="ccm-bc">';
	menuHTML += '<div id="ccm-bc-inner">';
	<?
		$nh = Loader::helper('navigation');
		$trail = $nh->getTrailToCollection($c);
		$trail = array_reverse($trail);
		$trail[] = $c;
	?>
	menuHTML += '<ul>';
	<? foreach($trail as $_c) { ?>
		menuHTML += '<li><a href="#" onclick="javascript:location.href=\'<?=$nh->getLinkToCollection($_c)?>\'"><?=htmlentities($_c->getCollectionName(), ENT_QUOTES, APP_CHARSET)?></a></li>';
	<? } ?>
	menuHTML += '</ul>';
	
	menuHTML += '</div>';
	menuHTML += '</div>';
<? } ?>

<?
if ($statusMessage != '') {?> 
	$(function() { ccmAlert.hud('<?=str_replace("'",'"',$statusMessage) ?>', 5000); });
<? } ?>

	
	$(function() {
		$(document.body).prepend('<div id="ccm-page-controls-wrapper"></div>');
		$("#ccm-page-controls-wrapper").html(menuHTML);
	
		<? if ($c->isArrangeMode()) { ?>
			$(ccm_arrangeInit);	
		<? } else if ($c->isEditMode()) { ?>
			$(ccm_editInit);	
		<? } else { ?>
			$(ccm_init);
		<? } ?>
		
		<? if ($u->config('UI_BREADCRUMB')) {  ?>
			ccm_setupBreadcrumb();
		<? } ?>
		
	});
<?
	}
	
} ?>

