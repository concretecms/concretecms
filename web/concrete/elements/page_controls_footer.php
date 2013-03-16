<?
defined('C5_EXECUTE') or die("Access Denied.");
$html = Loader::helper('html');
$dh = Loader::helper('concrete/dashboard');
$valt = Loader::helper('validation/token');
$token = '&' . $valt->getParameter();
$cID = $c->getCollectionID();

$workflowList = PageWorkflowProgress::getList($c);

$canViewToolbar = $cp->canViewToolbar();

if (isset($cp) && $canViewToolbar && (!$dh->inDashboard())) { 

	$canEditPageProperties = $cp->canEditPageProperties();
	$canPreviewPageAsUser = $cp->canPreviewPageAsUser();
	$canEditPageTheme = $cp->canEditPageTheme();
	$canEditPageContents = $cp->canEditPageContents();
	$canEditPageType = $cp->canEditPageType();
	$canViewPageVersions = $cp->canViewPageVersions();
	$canApprovePageVersions = $cp->canApprovePageVersions();
	$canEditPageSpeedSettings = $cp->canEditPageSpeedSettings();
	$canEditPagePermissions = $cp->canEditPagePermissions();
	$canMoveOrCopyPage = $cp->canMoveOrCopyPage();
	$canDeletePage = $cp->canDeletePage();

	$u = new User();
	$username = $u->getUserName();
	$vo = $c->getVersionObject();
	$pageInUseBySomeoneElse = false;

	if ($c->isCheckedOut()) {
		if (!$c->isCheckedOutByMe()) {
			$pageInUseBySomeoneElse = true;
		}
	}


	if ($c->isEditMode()) { 
		if ($vo->isNew()) {
			$publishToggle = '#ccm-exit-edit-mode-comment';
		} else {
			$publishToggle = '#ccm-exit-edit-mode-direct';
		}
	} else {
		$publishToggle = '#ccm-toolbar-menu-page-edit';
	}

	?>

	<div id="ccm-page-controls-wrapper" class="ccm-ui">
		<div id="ccm-toolbar">
			<ul>
				<li class="ccm-logo pull-left"><span><?=Loader::helper('concrete/interface')->getToolbarLogoSRC()?></span></li>
				<? if (!$pageInUseBySomeoneElse && $c->getCollectionPointerID() == 0) { ?>
				<li class="<? if ($c->isEditMode()) { ?> ccm-toolbar-page-edit-mode-active <? } ?> ccm-toolbar-page-edit pull-left"><a data-toggle="ccm-toolbar-hover-menu" data-toggle-menu="<?=$publishToggle?>" href="<? if (!$c->isEditMode()) { ?><?=DIR_REL?>/<?=DISPATCHER_FILENAME?>?cID=<?=$c->getCollectionID()?>&ctask=check-out<?=$token?><? } else { ?>javascript:void(0);<? } ?>"><i class="glyphicon glyphicon-pencil"></i></a>

				<? if ($c->isEditMode()) { ?>

				<div id="ccm-exit-edit-mode-comment" class="ccm-toolbar-hover-menu dropdown-menu">
					<form method="post" action="<?=DIR_REL?>/<?=DISPATCHER_FILENAME?>?cID=<?=$c->getCollectionID()?>&ctask=check-in">
					<div id="ccm-exit-edit-mode-comment-form">
						<?=$valt->output()?>
						<textarea name="comments" placeholder="<?=t('Write a Version Comment')?>"></textarea>
						<input type="hidden" name="approve" value="PREVIEW" id="ccm-approve-field" />
					</div>
					<div id="ccm-exit-edit-mode-publish-menu" class="ccm-toolbar-hover-menu-footer">
						<!--<a href=""><i class="glyphicon glyphicon-time"></i></a>//-->
						<ul>
							<? if ($canApprovePageVersions) { ?>
								<? 
								$publishTitle = t('Publish My Edits');
								$pk = PermissionKey::getByHandle('approve_page_versions');
								$pk->setPermissionObject($c);
								$pa = $pk->getPermissionAccessObject();
								if (is_object($pa) && count($pa->getWorkflows()) > 0) {
									$publishTitle = t('Submit to Workflow');
								}
							?>
								<li class="ccm-exit-edit-mode-publish"><a href="#" data-publish-action="approve"><?=$publishTitle?></a></li>
							<? } ?>
							<li><a href="#"><?=t('Save as Draft')?></a></li>
							<li class="ccm-exit-edit-mode-discard"><a href="#" data-publish-action="discard"><?=t('Discard Edits')?></a></li>
						</ul>
					</div>
					</form>

				</div>

				<ul id="ccm-exit-edit-mode-direct" class="ccm-toolbar-hover-menu dropdown-menu">
					<li><a href="javascript:void(0)" onclick="window.location.href='<?=DIR_REL?>/<?=DISPATCHER_FILENAME?>?cID=<?=$c->getCollectionID()?>&ctask=check-in<?=$token?>'" id="ccm-nav-exit-edit-direct"><?=t('Exit Edit Mode')?></a>
				</ul>

				<? } else { ?>

				<ul id="ccm-toolbar-menu-page-edit" class="ccm-toolbar-hover-menu dropdown-menu">
				<? if ($canEditPageContents) { ?>
					<li class="ccm-toolbar-hover-menu-edit"><a href="<?=DIR_REL?>/<?=DISPATCHER_FILENAME?>?cID=<?=$c->getCollectionID()?>&ctask=check-out<?=$token?>"><?=t('Edit Page')?></a></li>
					<li class="divider"></li>
				<? } ?>
				<li><a class="dialog-launch" dialog-width="645" dialog-modal="false" dialog-height="345" dialog-title="<?php echo t('New Page')?>" href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup.php?cID=<?php echo $cID?>&ctask=add"class="btn"><?php echo t('New Page')?></a></li>
				<li><a class="dialog-launch" dialog-width="645" dialog-modal="false" dialog-height="345" dialog-title="<?php echo t('Drafts')?>" href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/pages/drafts"class="btn"><?php echo t('Drafts')?></a></li>
				</ul>

				<? } ?>

				</li>
				<li class="ccm-toolbar-page-settings pull-left"><a href="#" onclick="return false" data-toggle="ccm-toolbar-hover-menu" data-toggle-menu="#ccm-toolbar-menu-page-settings"><i class="glyphicon glyphicon-cog"></i></a>

				<ul id="ccm-toolbar-menu-page-settings" class="ccm-toolbar-hover-menu dropdown-menu">
				<? if ($canEditPageProperties) { ?>
					<li><a class="dialog-launch" <? if (!$c->isCheckedOut()) { ?> dialog-on-close="ccm_sitemapExitEditMode(<?=$c->getCollectionID()?>)" <? } ?> dialog-width="850" dialog-height="<? if ($canApprovePageVersions && (!$c->isEditMode())) { ?>450<? } else { ?>390<? } ?>" dialog-modal="false" dialog-title="<?=t('Page Properties')?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup.php?<? if ($canApprovePageVersions && (!$c->isEditMode())) { ?>approveImmediately=1<? } ?>&cID=<?=$c->getCollectionID()?>&ctask=edit_metadata"><?=t('Properties')?></a></li>
				<? } ?>
				<? if ($canPreviewPageAsUser && PERMISSIONS_MODEL == 'advanced') { ?>
					<li><a class="dialog-launch" dialog-width="90%" dialog-height="70%" dialog-modal="false" dialog-title="<?=t('View Page as Someone Else')?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup.php?cID=<?=$c->getCollectionID()?>&ctask=preview_page_as_user"><?=t('Preview as User')?></a></li>
				<? } ?>
				<? if ($canEditPageTheme || $canEditPageType) { ?>
					<li><a class="dialog-launch" <? if (!$c->isCheckedOut()) { ?> dialog-on-close="ccm_sitemapExitEditMode(<?=$c->getCollectionID()?>)" <? } ?> dialog-width="610" dialog-height="405" dialog-modal="false" dialog-title="<?=t('Design')?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup.php?cID=<?=$cID?>&ctask=set_theme"><?=t('Design')?></a></li>
				<? } ?>
				<? if ($canEditPageProperties || ($canPreviewPageAsUser && PERMISSIONS_MODEL == 'advanced') || $canEditPageTheme || $canEditPageType) { ?>
					<li class="divider"></li>
				<? } ?>
				<? if ($canViewPageVersions) { ?>
					<li><a class="dialog-launch" <? if (!$c->isCheckedOut()) { ?> dialog-on-close="ccm_sitemapExitEditMode(<?=$c->getCollectionID()?>)" <? } ?> id="ccm-toolbar-nav-versions" dialog-width="640" dialog-height="340" dialog-modal="false" dialog-title="<?=t('Page Versions')?>" id="menuVersions<?=$cID?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/versions.php?cID=<?=$cID?>"><?=t('Versions')?></a></li>
				<? } ?>
				<? if ($canEditPageSpeedSettings) { ?>
					<li><a class="dialog-launch" <? if (!$c->isCheckedOut()) { ?> dialog-on-close="ccm_sitemapExitEditMode(<?=$c->getCollectionID()?>)" <? } ?> id="ccm-toolbar-nav-speed-settings" dialog-append-buttons="true" dialog-width="550" dialog-height="280" dialog-modal="false" dialog-title="<?=t('Full Page Caching')?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup.php?&cID=<?=$cID?>&ctask=edit_speed_settings"><?=t('Full Page Caching')?></a></li>
				<? } ?>
				<? if ($canEditPagePermissions) { ?>
					<li><a class="dialog-launch" <? if (!$c->isCheckedOut()) { ?> dialog-on-close="ccm_sitemapExitEditMode(<?=$c->getCollectionID()?>)" <? } ?> dialog-append-buttons="true" id="ccm-toolbar-nav-permissions" dialog-width="420" dialog-height="630" dialog-modal="false" dialog-title="<?=t('Permissions')?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup.php?&cID=<?=$cID?>&ctask=edit_permissions"><?=t('Permissions')?></a></li>
				<? } ?>
				<? if ($canViewPageVersions || $canEditPageSpeedSettings || $canEditPagePermissions) { ?>
					<li class="divider"></li>
				<? } ?>
				<? if ($canMoveOrCopyPage) { ?>
					<li><a class="dialog-launch" dialog-width="90%" dialog-height="70%" dialog-modal="false" dialog-title="<?=t('Move/Copy Page')?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/sitemap_search_selector?sitemap_select_mode=move_copy_delete&cID=<?=$cID?>"><?=t('Move/Copy')?></a></li>
				<? } ?>
				<? if ($canDeletePage) { ?>
					<li><a class="dialog-launch" <? if (!$c->isCheckedOut()) { ?> dialog-on-close="ccm_sitemapExitEditMode(<?=$c->getCollectionID()?>)" <? } ?>  dialog-append-buttons="true" id="ccm-toolbar-nav-delete" dialog-width="360" dialog-height="150" dialog-modal="false" dialog-title="<?=t('Delete Page')?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup.php?&cID=<?=$cID?>&ctask=delete"><?=t('Delete')?></a></li>
				<? } ?>
				</ul>

				</li>
				<? } ?>

				<li class="ccm-toolbar-account pull-right"><a href="#" data-toggle="ccm-toolbar-hover-menu" data-toggle-menu="#ccm-toolbar-menu-user"><i class="glyphicon glyphicon-user"></i></a>
				
				<ul id="ccm-toolbar-menu-user" class="ccm-toolbar-hover-menu dropdown-menu">
				  <li><a href="<?=$this->url('/account')?>"><?=t('Account')?></a></li>
				  <li><a href="<?=$this->url('/account/messages/inbox')?>"><?=t('Inbox')?></a></li>
				  <li><a href="<?=$this->url('/login', 'logout')?>">Sign Out</a></li>
				</ul>

				</li>
				<? if ($dh->canRead()) { ?>
					<li class="ccm-toolbar-dashboard pull-right"><a href="<?=$this->url('/dashboard')?>" data-toggle="ccm-toolbar-hover-menu" data-toggle-menu="#ccm-toolbar-menu-dashboard"><i class="glyphicon glyphicon-briefcase"></i></a>

					<?
					print $dh->addQuickNavToMenus($dh->getDashboardAndSearchMenus());
					?>

					</li>
				<? } ?>
				<li class="ccm-toolbar-search pull-right"><i class="glyphicon glyphicon-search"></i> <input type="search" id="ccm-nav-intelligent-search" tabindex="1" /></li>
				<? if ($c->isEditMode() && $cp->canEditPageContents()) { ?>
					<li class="ccm-toolbar-add pull-right"><a class="dialog-launch" title="<?=t('Add Block')?>" dialog-width="660" dialog-height="400" dialog-modal="false" dialog-title="<?=t('Add Block')?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/pages/add_block?cID=<?=$c->getCollectionID()?>"><i class="glyphicon glyphicon-plus"></i></a></li>
				<? } ?>

			</ul>

		</div>

	<? if ($pageInUseBySomeoneElse) { ?>
		<div id="ccm-page-status-bar">
			<div class="alert alert-info"><button type="button" class="close" data-dismiss="alert">×</button> <span><?= t("%s is currently editing this page.", $c->getCollectionCheckedOutUserName())?></span></div>
		</div>
	<? } ?>

	<? if ($c->getCollectionPointerID() > 0) { ?>

		<div id="ccm-page-status-bar">
			<div class="alert alert-info">
				<button type="button" class="close" data-dismiss="alert">×</button>
				<span><?= t("This page is an alias of one that actually appears elsewhere.")?></span>
				<div class="ccm-page-status-bar-buttons">
					<a href="<?=DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID()?>" class="btn btn-mini"><?=t('View/Edit Original')?></a>
					<? if ($canApprovePageVersions) { ?>
						<a href="<?=DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionPointerOriginalID() . "&ctask=remove-alias" . $token?>" class="btn btn-mini btn-danger"><?=t('Remove Alias')?></a>
					<? } ?>
				</div>
			</div>
		</div>

	<? }

	if ($c->isMasterCollection()) { ?>

		<div id="ccm-page-status-bar">
			<div class="alert alert-info"><button type="button" class="close" data-dismiss="alert">×</button> <span><?= t('Page Defaults for %s Page Type. All edits take effect immediately.', $c->getCollectionTypeName()) ?></span></div>
		</div>

	<? }
	
	$hasPendingPageApproval = false;
	
	if ($canViewToolbar) { ?>
		<? if (is_array($workflowList)) { ?>
			<div id="ccm-page-status-bar">
			<? foreach($workflowList as $i => $wl) { ?>
				<? $wr = $wl->getWorkflowRequestObject(); 
				$wrk = $wr->getWorkflowRequestPermissionKeyObject(); 
				if ($wrk->getPermissionKeyHandle() == 'approve_page_versions') {
					$hasPendingPageApproval = true;
				}
				?>
				<? $wf = $wl->getWorkflowObject(); ?>
				<form method="post" action="<?=$wl->getWorkflowProgressFormAction()?>" id="ccm-status-bar-form-<?=$i?>" class="ccm-status-bar-ajax-form">
					<div class="alert alert-<?=$wr->getWorkflowRequestStyleClass()?>"><button type="button" class="close" data-dismiss="alert">×</button> <span><?=$wf->getWorkflowProgressCurrentDescription($wl)?></span>
					<? $actions = $wl->getWorkflowProgressActions(); ?>
					<? if (count($actions) > 0) { ?>
						<div class="ccm-page-status-bar-buttons">
						<? foreach($actions as $act) { ?>
							<? if ($act->getWorkflowProgressActionURL() != '') { ?>
								<a href="<?=$act->getWorkflowProgressActionURL()?>" 
							<? } else { ?>
								<button type="submit" name="action_<?=$act->getWorkflowProgressActionTask()?>" 
							<? } ?>

							<? if (count($act->getWorkflowProgressActionExtraButtonParameters()) > 0) { ?>
								<? foreach($act->getWorkflowProgressActionExtraButtonParameters() as $key => $value) { ?>
									<?=$key?>="<?=$value?>" 
								<? } ?>
							<? } ?>

							 class="btn btn-mini <?=$act->getWorkflowProgressActionStyleClass()?>"><?=$act->getWorkflowProgressActionStyleInnerButtonLeftHTML()?> <?=$act->getWorkflowProgressActionLabel()?> <?=$act->getWorkflowProgressActionStyleInnerButtonRightHTML()?>
							<? if ($act->getWorkflowProgressActionURL() != '') { ?>
								</a>
							<? } else { ?>
								</button>
							<? } ?>
						<? } ?>
						</div>
					<? } ?>	
					</div>				
				</form>
				<? } ?>
			</div>
		<? } ?>
	<? }

	if (!$c->getCollectionPointerID() && !$hasPendingPageApproval) {
		if (is_object($vo)) {
			if (!$vo->isApproved() && !$c->isEditMode()) { ?>

			<div id="ccm-page-status-bar">
				<div class="alert alert-info">
					<button type="button" class="close" data-dismiss="alert">×</button>
					<span><?= t("This page is pending approval.")?></span>
					<? if ($canApprovePageVersions && !$c->isCheckedOut()) { ?>
					<div class="ccm-page-status-bar-buttons">
						<?
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
						<a href="<?=DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "&ctask=approve-recent" . $token?>" class="btn btn-mini"><?=$appLabel?> <i class="glyphicon glyphicon-thumbs-up"></i></a>
					</div>
					<? } ?>
				</div>
			</div>
			<? }
		}
	} ?>		
	</div>

<? }