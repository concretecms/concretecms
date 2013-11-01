<?
defined('C5_EXECUTE') or die("Access Denied.");
header('Content-type: text/javascript');?>

var menuHTML = '';

<?
Loader::library('3rdparty/mobile_detect');
$md = new Mobile_Detect();

if ($_REQUEST['cvID'] > 0) {
	$c = Page::getByID($_REQUEST['cID'], $_REQUEST['cvID']);
} else {
	$c = Page::getByID($_REQUEST['cID']);
}
$cp = new Permissions($c);
$req = Request::getInstance();
$req->setCurrentPage($c);

$valt = Loader::helper('validation/token');
$sh = Loader::helper('concrete/dashboard/sitemap');
$dh = Loader::helper('concrete/dashboard');
$ish = Loader::helper('concrete/interface');
$token = '&' . $valt->getParameter();

$workflowList = PageWorkflowProgress::getList($c);

if (isset($cp)) {

	$u = new User();
	$username = $u->getUserName();
	$vo = $c->getVersionObject();

	if ($c->isCheckedOut()) {
		if (!$c->isCheckedOutByMe()) {
			$cantCheckOut = true;
		}
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
	
	menuHTML += '<li <? if ($c->isEditMode()) { ?>class="ccm-nav-edit-mode-active"<? } ?>><a class="ccm-icon-edit ccm-menu-icon" id="ccm-nav-edit" href="<? if (!$c->isEditMode()) { ?><?=DIR_REL?>/<?=DISPATCHER_FILENAME?>?cID=<?=$c->getCollectionID()?>&ctask=check-out<?=$token?><? } else { ?>javascript:void(0);<? } ?>" <? if (!$c->isEditMode()) { ?> onclick="$(\'#ccm-edit-overlay\').hide()" <? } ?>><? if ($c->isEditMode()) { ?><?=t('Editing')?><? } else { ?><?=t('Edit')?><? } ?></a></li>';
	<? if (!$c->isEditMode() && $cp->canAddSubpage()) { ?>menuHTML += '<li><a href="<?=DIR_REL?>/<?=DISPATCHER_FILENAME?>?cID=<?=$c->getCollectionID()?>&ctask=create-draft<?=$token?>"><i class="icon-plus-sign"></i> <?=t('Add Page')?></a></li>';<? } ?>
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
	menuHTML += '<li><a class="ccm-icon-dashboard ccm-menu-icon" id="ccm-nav-dashboard<? if ($md->isMobile()) { ?>-mobile<? } ?>" href="<?=View::url('/dashboard')?>"><?=t('Dashboard')?></a></li>';
<? } 

if (defined('ENABLE_USER_PROFILES') && ENABLE_USER_PROFILES) {
	$account = Page::getByPath('/account');
	if (is_object($account) && !$account->isError()) {

 ?>
	menuHTML += '<li id="ccm-nav-my-account"><a href="javascript:void(0)"  data-toggle="dropdown"><i class="icon-user"></i> <?=t("My Account")?></a>';
	menuHTML += '</li>';
<? } 

}?>
menuHTML += '<li id="ccm-nav-intelligent-search-wrapper"><input type="search" placeholder="<?=t('Intelligent Search')?>" id="ccm-nav-intelligent-search" tabindex="1" /></li>';
menuHTML += '<li><a id="ccm-nav-sign-out" class="ccm-icon-sign-out ccm-menu-icon" href="<?=View::url('/login', 'logout')?>"><?=t('Sign Out')?></a></li>';
menuHTML += '</ul>';

menuHTML += '</div>';

<?
$dh = Loader::helper('concrete/dashboard');
?>

menuHTML += '<?=addslashes($dh->addQuickNavToMenus($dh->getDashboardAndSearchMenus()))?>';

menuHTML += '<div id="ccm-edit-overlay">';
menuHTML += '<div class="ccm-edit-overlay-inner">';

<? if ($c->isEditMode()) { ?>

menuHTML += '<div id="ccm-exit-edit-mode-direct" <? if ($vo->isNew()) { ?>style="display: none"<? } ?>>';
menuHTML += '<div class="ccm-edit-overlay-actions">';
menuHTML += '<a href="javascript:void(0)" onclick="window.location.href=\'<?=DIR_REL?>/<?=DISPATCHER_FILENAME?>?cID=<?=$c->getCollectionID()?>&ctask=check-in<?=$token?>\'" id="ccm-nav-exit-edit-direct" class="btn btn-primary"><?=t('Exit Edit Mode')?></a>';
menuHTML += '</div>';
menuHTML += '<span class="label notice"><?=t('Version %s', $c->getVersionID())?></span>';
menuHTML += '<?=t('Page currently in edit mode on %s', date(DATE_APP_GENERIC_MDYT))?>';

menuHTML += '</div>';

menuHTML += '<div id="ccm-exit-edit-mode-comment" <? if (!$vo->isNew()) { ?>style="display: none"<? } ?>>';
menuHTML += '<div class="ccm-edit-overlay-actions clearfix">';
menuHTML += '<form method="post" id="ccm-check-in" action="<?=DIR_REL?>/<?=DISPATCHER_FILENAME?>?cID=<?=$c->getCollectionID()?>&ctask=check-in">';
<? $valt = Loader::helper('validation/token'); ?>
menuHTML += '<?=$valt->output('', true)?>';
menuHTML += '<h4><?=t('Version Comments')?></h4>';
menuHTML += '<p><input type="text" name="comments" id="ccm-check-in-comments" style="width:520px" maxlength="255" /></p>';
<? if ($cp->canApprovePageVersions()) { ?>
	<? 
	$publishTitle = t('Publish My Edits');
	$pk = PermissionKey::getByHandle('approve_page_versions');
	$pk->setPermissionObject($c);
	$pa = $pk->getPermissionAccessObject();
	if (is_object($pa) && count($pa->getWorkflows()) > 0) {
		$publishTitle = t('Submit to Workflow');
	}
?>
menuHTML += '<a href="javascript:void(0)" id="ccm-check-in-publish" class="btn btn-primary" style="float: right"><span><?=$publishTitle?></span></a>';
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

menuHTML += '<span class="label notice"><?=t('Version %s', $c->getVersionID())?></span>';
menuHTML += '<?=t('Page last edited on %s', $c->getCollectionDateLastModified(DATE_APP_GENERIC_MDYT))?>';


<? } ?>

menuHTML += '</div>';

<? if (!$cantCheckOut && !$c->isEditMode()) { ?>

menuHTML += '<div id="ccm-edit-overlay-footer">';
menuHTML += '<div class="ccm-edit-overlay-inner">';
menuHTML += '<ul>';
<? if ($cp->canEditPageProperties()) { ?>
	menuHTML += '<li><a class="ccm-menu-icon ccm-icon-properties" <? if (!$c->isCheckedOut()) { ?> dialog-on-close="ccm_sitemapExitEditMode(<?=$c->getCollectionID()?>)" <? } ?> id="ccm-toolbar-nav-properties" dialog-width="850" dialog-height="<? if ($cp->canApprovePageVersions() && (!$c->isEditMode())) { ?>450<? } else { ?>390<? } ?>" dialog-append-buttons="true" dialog-modal="false" dialog-title="<?=t('Page Properties')?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup.php?<? if ($cp->canApprovePageVersions() && (!$c->isEditMode())) { ?>approveImmediately=1<? } ?>&cID=<?=$c->getCollectionID()?>&ctask=edit_metadata"><?=t('Properties')?></a></li>';
<? } ?>
<? if ($cp->canPreviewPageAsUser() && PERMISSIONS_MODEL == 'advanced') { ?>
	menuHTML += '<li><a class="ccm-menu-icon ccm-icon-preview-as-user" <? if (!$c->isCheckedOut()) { ?> dialog-on-close="ccm_sitemapExitEditMode(<?=$c->getCollectionID()?>)" <? } ?> id="ccm-toolbar-nav-preview-as-user" dialog-width="90%" dialog-height="70%" dialog-append-buttons="true" dialog-modal="false" dialog-title="<?=t('View Page as Someone Else')?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup.php?cID=<?=$c->getCollectionID()?>&ctask=preview_page_as_user"><?=t('Preview as User')?></a></li>';
<? } ?>
<? if ($cp->canEditPageTheme() || $cp->canEditPageType()) { ?>
	menuHTML += '<li><a class="ccm-menu-icon ccm-icon-design" <? if (!$c->isCheckedOut()) { ?> dialog-on-close="ccm_sitemapExitEditMode(<?=$c->getCollectionID()?>)" <? } ?> id="ccm-toolbar-nav-design" dialog-append-buttons="true" dialog-width="610" dialog-height="405" dialog-modal="false" dialog-title="<?=t('Design')?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup.php?cID=<?=$cID?>&ctask=set_theme"><?=t('Design')?></a></li>';
<? } ?>
<? if ($cp->canEditPagePermissions()) { ?>
	menuHTML += '<li><a class="ccm-menu-icon ccm-icon-permissions" <? if (!$c->isCheckedOut()) { ?> dialog-on-close="ccm_sitemapExitEditMode(<?=$c->getCollectionID()?>)" <? } ?> dialog-append-buttons="true" id="ccm-toolbar-nav-permissions" dialog-width="420" dialog-height="630" dialog-modal="false" dialog-title="<?=t('Permissions')?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup.php?&cID=<?=$cID?>&ctask=edit_permissions"><?=t('Permissions')?></a></li>';
<? } ?>
<? if ($cp->canViewPageVersions()) { ?>
	menuHTML += '<li><a class="ccm-menu-icon ccm-icon-versions" <? if (!$c->isCheckedOut()) { ?> dialog-on-close="ccm_sitemapExitEditMode(<?=$c->getCollectionID()?>)" <? } ?> id="ccm-toolbar-nav-versions" dialog-width="640" dialog-height="340" dialog-modal="false" dialog-title="<?=t('Page Versions')?>" id="menuVersions<?=$cID?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/versions.php?cID=<?=$cID?>"><?=t('Versions')?></a></li>';
<? } ?>
<? if ($cp->canMoveOrCopyPage()) { ?>
	menuHTML += '<li><a class="ccm-menu-icon ccm-icon-move-copy" id="ccm-toolbar-nav-move-copy" dialog-width="90%" dialog-height="70%" dialog-modal="false" dialog-title="<?=t('Move/Copy Page')?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/sitemap_search_selector?sitemap_select_mode=move_copy_delete&cID=<?=$cID?>"><?=t('Move/Copy')?></a></li>';
<? } ?>
<? if ($cp->canEditPageSpeedSettings()) { ?>
	menuHTML += '<li><a class="ccm-menu-icon ccm-icon-speed-settings" <? if (!$c->isCheckedOut()) { ?> dialog-on-close="ccm_sitemapExitEditMode(<?=$c->getCollectionID()?>)" <? } ?> id="ccm-toolbar-nav-speed-settings" dialog-append-buttons="true" dialog-width="550" dialog-height="280" dialog-modal="false" dialog-title="<?=t('Full Page Caching')?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup.php?&cID=<?=$cID?>&ctask=edit_speed_settings"><?=t('Full Page Caching')?></a></li>';
<? } ?>
<? if ($cp->canDeletePage()) { ?>
	menuHTML += '<li><a class="ccm-menu-icon ccm-icon-delete" <? if (!$c->isCheckedOut()) { ?> dialog-on-close="ccm_sitemapExitEditMode(<?=$c->getCollectionID()?>)" <? } ?>  dialog-append-buttons="true" id="ccm-toolbar-nav-delete" dialog-width="360" dialog-height="150" dialog-modal="false" dialog-title="<?=t('Delete Page')?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup.php?&cID=<?=$cID?>&ctask=delete"><?=t('Delete')?></a></li>';
<? } ?>
menuHTML += '</ul>';
menuHTML += '</div>';
menuHTML += '</div>';

<? } ?>

menuHTML += '</div>';
<?
	}
	
} ?>

<? if ($c->isEditMode()) { ?>
	menuHTML += '<ul class="ccm-sub-toolbar" id="ccm-edit-page-sub-toolbar">';
	<? if ($cp->canEditPageProperties()) { ?>
		menuHTML += '<li class="ccm-sub-toolbar-icon-cell"><a dialog-width="850" title="<?=t('Edit Properties')?>" dialog-height="450" dialog-append-buttons="true" dialog-modal="false" class="dialog-launch" dialog-title="<?=t('Page Properties')?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup.php?<? if ($cp->canApprovePageVersions() && (!$c->isEditMode())) { ?>approveImmediately=1<? } ?>&cID=<?=$c->getCollectionID()?>&ctask=edit_metadata"><i class="icon-cog"></i></a></li>';
	<? } ?>
	<? if ($cp->canEditPageTheme() || $cp->canEditPageType()) { ?>
		menuHTML += '<li class="ccm-sub-toolbar-icon-cell"><a class="dialog-launch" title="<?=t('Change Theme or Page Type')?>" dialog-width="610" dialog-height="405" dialog-modal="false" dialog-title="<?=t('Design')?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup.php?cID=<?=$cID?>&ctask=set_theme"><i class="icon-font"></i></a></li>';
	<? } ?>
	<? if ($cp->canEditPageProperties()) { ?>
		menuHTML += '<li class="ccm-sub-toolbar-icon-cell"><a class="dialog-launch" title="<?=t('Add Block')?>" dialog-width="660" dialog-height="280" dialog-modal="false" dialog-title="<?=t('Add Block')?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/pages/add_block?cID=<?=$cID?>"><i class="icon-plus"></i></a></li>';
	<? } ?>
	<? if ($cp->canEditPageTheme() || $cp->canEditPageType() || $cp->canEditPageProperties()) { ?>
		menuHTML += '<li class="ccm-sub-toolbar-separator"></li>';
	<? } ?>
	<? if ($cp->canViewPageVersions()) { ?>
		menuHTML += '<li class="ccm-sub-toolbar-icon-cell"><a class="dialog-launch" dialog-width="640" dialog-height="340" dialog-modal="false" title="<?=t('Page Versions')?>" dialog-title="<?=t('Page Versions')?>" id="menuVersions<?=$cID?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/versions.php?cID=<?=$cID?>"><i class="icon-th-list"></i></a></li>';
	<? } ?>
	<? if ($cp->canEditPageSpeedSettings()) { ?>
		menuHTML += '<li class="ccm-sub-toolbar-icon-cell"><a class="dialog-launch" dialog-width="550" dialog-height="280" dialog-modal="false" title="<?=t('Speed Settings')?>" dialog-title="<?=t('Speed Settings')?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup.php?&cID=<?=$cID?>&ctask=edit_speed_settings"><i class="icon-fire"></i></a></li>';
	<? } ?>
	<? if ($cp->canEditPagePermissions()) { ?>
		menuHTML += '<li class="ccm-sub-toolbar-icon-cell"><a class="dialog-launch" title="<?=t('Page Permissions')?>" dialog-width="420" dialog-height="630" dialog-modal="false" dialog-title="<?=t('Permissions')?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup.php?&cID=<?=$cID?>&ctask=edit_permissions"><i class="icon-lock"></i> </a></li>';
	<? } ?>
	<? if (($cp->canPreviewPageAsUser() && PERMISSIONS_MODEL == 'advanced')) { ?>
		menuHTML += '<li class="ccm-sub-toolbar-icon-cell"><a class="dialog-launch" dialog-width="90%" dialog-height="70%" title="<?=t('Preview as User')?>" dialog-append-buttons="true" dialog-modal="false" dialog-title="<?=t('View Page as Someone Else')?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup.php?cID=<?=$c->getCollectionID()?>&ctask=preview_page_as_user"><i class="icon-eye-open"></i></a></li>';
	<? } ?>
	<? if ($cp->canViewPageVersions() || $cp->canEditPageSpeedSettings() || $cp->canEditPagePermissions() || ($cp->canPreviewPageAsUser && PERMISSIONS_MODEL == 'advanced')) { ?>
		menuHTML += '<li class="ccm-sub-toolbar-separator"></li>';
		<? } ?>
	<? if ($cp->canMoveOrCopyPage()) { ?>
		menuHTML += '<li class="ccm-sub-toolbar-icon-cell"><a class="dialog-launch" title="<?=t('Move/Copy')?>" ialog-width="90%" dialog-height="70%" dialog-modal="false" dialog-title="<?=t('Move/Copy Page')?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/sitemap_search_selector?sitemap_select_mode=move_copy_delete&cID=<?=$cID?>"><i class="icon-share-alt"></i></a></li>';
	<? } ?>
	<? if ($cp->canDeletePage()) { ?>
		menuHTML += '<li class="ccm-sub-toolbar-icon-cell"><a class="dialog-launch" title="<?=t('Delete Page')?>" dialog-width="360" dialog-height="150" dialog-modal="false" dialog-title="<?=t('Delete Page')?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup.php?&cID=<?=$cID?>&ctask=delete"><i class="icon-trash"></i></a></li>';
	<? } ?>
	menuHTML += '</ul>';

<? } ?>


$(function() {
	<? if ($c->isEditMode()) { ?>
		$(ccm_editInit);	
	<? } ?>
	
	<?
	if (!$dh->inDashboard()) { ?>
		$("#ccm-page-controls-wrapper").html(menuHTML); 
		<? if (defined('ENABLE_USER_PROFILES') && ENABLE_USER_PROFILES) { ?>
		   $('#ccm-account-menu ul.dropdown-menu').appendTo('#ccm-nav-my-account').removeClass('pull-right');
		   $('#ccm-nav-my-account a').hover(function() {
				$(this).parent().addClass('open');
		   });
			$('#ccm-account-menu').remove();
		<? } ?>
		
		<? if ($cantCheckOut) { ?>
			item = new ccm_statusBarItem();
			item.setCSSClass('info');
			item.setDescription('<?= t("%s is currently editing this page.", $c->getCollectionCheckedOutUserName())?>');
			ccm_statusBar.addItem(item);		
		<? } ?>

		<? if ($c->getCollectionPointerID() > 0) { ?>
	
			sbitem  = new ccm_statusBarItem();
			sbitem.setCSSClass('info');
			sbitem.setDescription('<?= t("This page is an alias of one that actually appears elsewhere.", $c->getCollectionCheckedOutUserName())?>');
			btn1 = new ccm_statusBarItemButton();
			btn1.setLabel('<?=t('View/Edit Original')?>');
			btn1.setURL('<?=DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID()?>');
			sbitem.addButton(btn1);
			<? if ($cp->canApprovePageVersions()) { ?>
				btn2 = new ccm_statusBarItemButton();
				btn2.setLabel('<?=t('Remove Alias')?>');
				btn2.setCSSClass('danger');
				btn2.setURL('<?=DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionPointerOriginalID() . "&ctask=remove-alias" . $token?>');
				sbitem.addButton(btn2);
			<? } ?>
			ccm_statusBar.addItem(sbitem);		
		
		<? } 	

		if ($c->isMasterCollection()) { ?>

			sbitem = new ccm_statusBarItem();
			sbitem.setCSSClass('info');
			sbitem.setDescription('<?= t('Page Defaults for %s Page Type. All edits take effect immediately.', $c->getPageTypeName()) ?>');
			ccm_statusBar.addItem(sbitem);		
		<? } ?>
		<?
		$hasPendingPageApproval = false;
		
		if ($cp->canViewToolbar()) { ?>
			<? if (is_array($workflowList)) { ?>
				<? foreach($workflowList as $wl) { ?>
					<? $wr = $wl->getWorkflowRequestObject(); 
					$wrk = $wr->getWorkflowRequestPermissionKeyObject(); 
					if ($wrk->getPermissionKeyHandle() == 'approve_page_versions') {
						$hasPendingPageApproval = true;
					}
					?>
					<? $wf = $wl->getWorkflowObject(); ?>
					sbitem = new ccm_statusBarItem();
					sbitem.setCSSClass('<?=$wr->getWorkflowRequestStyleClass()?>');
					sbitem.setDescription('<?=$wf->getWorkflowProgressCurrentDescription($wl)?>');
					sbitem.setAction('<?=$wl->getWorkflowProgressFormAction()?>');
					sbitem.enableAjaxForm();
					<? $actions = $wl->getWorkflowProgressActions(); ?>
					<? foreach($actions as $act) { ?>
						btn = new ccm_statusBarItemButton();
						btn.setLabel('<?=$act->getWorkflowProgressActionLabel()?>');
						btn.setCSSClass('<?=$act->getWorkflowProgressActionStyleClass()?>');
						btn.setInnerButtonLeftHTML('<?=$act->getWorkflowProgressActionStyleInnerButtonLeftHTML()?>');
						btn.setInnerButtonRightHTML('<?=$act->getWorkflowProgressActionStyleInnerButtonRightHTML()?>');
						<? if ($act->getWorkflowProgressActionURL() != '') { ?>
							btn.setURL('<?=$act->getWorkflowProgressActionURL()?>');
						<? } else { ?>
							btn.setAction('<?=$act->getWorkflowProgressActionTask()?>');
						<? } ?>
						<? if (count($act->getWorkflowProgressActionExtraButtonParameters()) > 0) { ?>
							<? foreach($act->getWorkflowProgressActionExtraButtonParameters() as $key => $value) { ?>
								btn.addAttribute('<?=$key?>', '<?=$value?>');
							<? } ?>
						<? } ?>
						sbitem.addButton(btn);
					<? } ?>
					ccm_statusBar.addItem(sbitem);
				<? } ?>
			
			<? } ?>
		<? } ?>
		
		<?		
		
		if (!$c->getCollectionPointerID() && !$hasPendingPageApproval) {
			if (is_object($vo)) {
				if (!$vo->isApproved() && !$c->isEditMode()) { ?>
				
					sbitem = new ccm_statusBarItem();
					sbitem.setCSSClass('info');
					sbitem.setDescription('<?= t("This page is pending approval.")?>');
					<? if ($cp->canApprovePageVersions() && !$c->isCheckedOut()) { 
						$pk = PagePermissionKey::getByHandle('approve_page_versions');
						$pk->setPermissionObject($c);
						$pa = $pk->getPermissionAccessObject();
						if (is_object($pa)) {
							if (count($pa->getWorkflows()) > 0) {
								$appLabel = t('Submit for Approval');
							}
						}
						if (!$appLabel) {
							$appLabel = t('Approve Version');
						}
						?>
						btn1 = new ccm_statusBarItemButton();
						btn1.setCSSClass('btn-success');
						btn1.setLabel('<?=$appLabel?> <i class="icon-thumbs-up icon-white"></i>');
						btn1.setURL('<?=DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "&ctask=approve-recent" . $token?>');
						sbitem.addButton(btn1);
					<? } ?>
					ccm_statusBar.addItem(sbitem);		
				<? }
			}
		} ?>		
		

		ccm_statusBar.activate();		
		$(".launch-tooltip").tooltip();
		ccm_activateToolbar();
	<? } ?>
	
	

	
});
