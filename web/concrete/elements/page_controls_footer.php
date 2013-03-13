<?
defined('C5_EXECUTE') or die("Access Denied.");
$html = Loader::helper('html');
$dh = Loader::helper('concrete/dashboard');
$valt = Loader::helper('validation/token');
$token = '&' . $valt->getParameter();
$cID = $c->getCollectionID();

if (isset($cp) && $cp->canViewToolbar() && (!$dh->inDashboard())) { 

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

	?>

	<div id="ccm-page-controls-wrapper" class="ccm-ui">
		<div id="ccm-toolbar">
			<ul>
				<li class="ccm-logo pull-left"><span><?=Loader::helper('concrete/interface')->getToolbarLogoSRC()?></span></li>
				<? if (!$pageInUseBySomeoneElse) { ?>
				<li class="ccm-toolbar-page-edit pull-left"><a data-toggle="ccm-toolbar-hover-menu" data-toggle-menu="#ccm-toolbar-menu-page-edit" href="<? if (!$c->isEditMode()) { ?><?=DIR_REL?>/<?=DISPATCHER_FILENAME?>?cID=<?=$c->getCollectionID()?>&ctask=check-out<?=$token?><? } else { ?>javascript:void(0);<? } ?>"><i class="glyphicon glyphicon-pencil"></i></a>

				<? if ($c->isEditMode()) { ?>

				<div id="ccm-toolbar-menu-page-edit" class="ccm-toolbar-hover-menu dropdown-menu">
					<div class="ccm-toolbar-hover-menu-inner">

					</div>
				</div>

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
					<li class="ccm-toolbar-dashboard pull-right"><a href="<?=$this->url('/dashboard')?>" data-toggle="ccm-toolbar-hover-menu" data-toggle-menu="#ccm-toolbar-menu-dashboard"><i class="glyphicon glyphicon-briefcase"></i></a></li>
				<? } ?>
				<li class="ccm-toolbar-search pull-right"><i class="glyphicon glyphicon-search"></i> <input type="search" id="ccm-nav-intelligent-search" tabindex="1" /></li>
				<? if ($c->isEditMode() && $cp->canEditPageContents()) { ?>
					<li class="ccm-toolbar-add pull-right"><a class="dialog-launch" title="<?=t('Add Block')?>" dialog-width="660" dialog-height="280" dialog-modal="false" dialog-title="<?=t('Add Block')?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/pages/add_block?cID=<?=$c->getCollectionID()?>"><i class="glyphicon glyphicon-plus"></i></a></li>
				<? } ?>

			</ul>

		</div>
	</div>


<? }