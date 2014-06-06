<?
defined('C5_EXECUTE') or die("Access Denied.");
$html = Loader::helper('html');
$dh = Loader::helper('concrete/dashboard');
$ihm = Loader::helper('concrete/ui/menu');
$valt = Loader::helper('validation/token');
$token = '&' . $valt->getParameter();
$logouttoken = Loader::helper('validation/token')->generate('logout');
$cID = $c->getCollectionID();

$workflowList = \Concrete\Core\Workflow\Progress\PageProgress::getList($c);

$canViewToolbar = $cp->canViewToolbar();

if (isset($cp) && $canViewToolbar && (!$dh->inDashboard())) { 

	$canApprovePageVersions = $cp->canApprovePageVersions();
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
				<li class="ccm-logo pull-left"><span><?=Loader::helper('concrete/ui')->getToolbarLogoSRC()?></span></li>
				<? if ($c->isMasterCollection()) { ?>
					<li class="pull-left"><a href="<?=View::url('/dashboard/pages/types')?>"><i class="fa fa-arrow-left"></i></a>
				<? } ?>
				<? if (!$pageInUseBySomeoneElse && $c->getCollectionPointerID() == 0) { ?>

				<? if ($c->isEditMode()) { ?>
					<li class="ccm-toolbar-page-edit-mode-active ccm-toolbar-page-edit pull-left"><a data-toolbar-action="check-in" <? if ($vo->isNew()) { ?>href="javascript:void(0)" data-launch-panel="check-in"<? } else { ?>href="<?=URL::to('/ccm/system/page/check_in', $c->getCollectionID(), Loader::helper('validation/token')->generate())?>"<? } ?> data-panel-url="<?=URL::to('/ccm/system/panels/page/check_in')?>"><i class="fa fa-pencil"></i></a></li>
				<? } else { ?>
					<li class="ccm-toolbar-page-edit pull-left"><a data-toolbar-action="check-out" href="<?=DIR_REL?>/<?=DISPATCHER_FILENAME?>?cID=<?=$c->getCollectionID()?>&ctask=check-out<?=$token?>"><i class="fa fa-pencil"></i></a></li>
				<? } ?>

				<li class="pull-left"><a href="#" data-launch-panel="page" data-panel-url="<?=URL::to('/ccm/system/panels/page')?>"><i class="fa fa-cog"></i></a>

				</li>
				<? }

				if ($cp->canEditPageContents() && (!$pageInUseBySomeoneElse)) { ?>
					<li class="ccm-toolbar-add pull-left">
						<? if ($c->isEditMode()) { ?>
							<a href="#" data-launch-panel="add-block" data-panel-url="<?=URL::to('/ccm/system/panels/add')?>"><i class="fa fa-plus"></i></a>
						<? } else { ?>
							<a href="<?=DIR_REL?>/<?=DISPATCHER_FILENAME?>?cID=<?=$cID?>&ctask=check-out-add-block<?=$token?>"><i class="fa fa-plus"></i></a>
						<? } ?>
					</li>
				<? } 

					
				$items = $ihm->getPageHeaderMenuItems('left');
				foreach($items as $ih) {
					$cnt = $ih->getController(); 
					if ($cnt->displayItem()) {
					?>
						<li class="pull-left"><?=$cnt->getMenuLinkHTML()?></li>
					<?
					}
				}
				
				if (Loader::helper('concrete/ui')->showWhiteLabelMessage()) { ?>
					<li class="pull-left" id="ccm-white-label-message"><?=t('Powered by <a href="%s">concrete5</a>.', CONCRETE5_ORG_URL)?></li>
				<? }?>

				<li class="pull-right"><a href="<?=URL::to('/dashboard')?>" data-launch-panel="dashboard"><i class="fa fa-th-large"></i></a>

				<li class="pull-right"><a href="#" data-panel-url="<?=URL::to('/ccm/system/panels/sitemap')?>" data-launch-panel="sitemap"><i class="fa fa-list-alt"></i></a>

					</li>
				<li class="ccm-toolbar-search pull-right"><i class="fa fa-search"></i> <input type="search" id="ccm-nav-intelligent-search" tabindex="1" /></li>
				<?
				$items = $ihm->getPageHeaderMenuItems('right');
				foreach($items as $ih) {
					$cnt = $ih->getController(); 
					if ($cnt->displayItem()) {
					?>
						<li class="pull-right"><?=$cnt->getMenuLinkHTML()?></li>
					<?
					}
				}

				?>

			</ul>

		</div>

	<?
	print $dh->getIntelligentSearchMenu();
	?>

	<? if ($pageInUseBySomeoneElse) { ?>
		<?=Loader::helper('concrete/ui')->notify(array(
			'title' => t('Editing Unavailable.'),
			'message' => t("%s is currently editing this page.", $c->getCollectionCheckedOutUserName()),
			'type' => 'info',
			'icon' => 'exclamation-sign'
		))?>
	<? } else { ?>

	<? if ($c->getCollectionPointerID() > 0) { ?>

		<?
		$buttons = array();
		$buttons[] = '<a href="' . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $c->getCollectionID() . '" class="btn btn-default btn-xs">' . t('View/Edit Original') . '</a>';
		if ($canApprovePageVersions) {
			$buttons[] = '<a href="' . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $c->getCollectionPointerOriginalID() . '&ctask=remove-alias' . $token .'" class="btn btn-xs btn-danger">' . t('Remove Alias') . '</a>';
		}

		print Loader::helper('concrete/ui')->notify(array(
			'title' => t('Page Alias.'),
			'message' => t("This page is an alias of one that actually appears elsewhere."),
			'type' => 'info',
			'icon' => 'info-sign',
			'buttons' => $buttons
		))?>

	<? }

	if ($c->isMasterCollection()) {
		print Loader::helper('concrete/ui')->notify(array(
			'title' => t('Page Type Template.'),
			'message' => t('Page Defaults for %s Page Type. All edits take effect immediately.', $c->getPageTypeName()),
			'type' => 'info',
			'icon' => 'info-sign',
		))?>

	<? }
	
	$hasPendingPageApproval = false;
	
	if ($canViewToolbar) { ?>
		<? if (is_array($workflowList) && count($workflowList) > 0) { ?>
			<div id="ccm-notification-page-alert" class="ccm-notification ccm-notification-info">
			<div class="ccm-notification-inner-wrapper">
			<? foreach($workflowList as $i => $wl) { ?>
				<? $wr = $wl->getWorkflowRequestObject(); 
				$wf = $wl->getWorkflowObject(); ?>
				
				<form method="post" action="<?=$wl->getWorkflowProgressFormAction()?>" id="ccm-notification-page-alert-form-<?=$i?>">
					<i class="fa fa-info-circle"></i>
					<div class="ccm-notification-inner">
						<p><?=$wf->getWorkflowProgressCurrentDescription($wl)?></p>
					<? $actions = $wl->getWorkflowProgressActions(); ?>
					<? if (count($actions) > 0) { ?>
						<div class="ccm-notification-inner-buttons">
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

							 class="btn btn-xs <?=$act->getWorkflowProgressActionStyleClass()?>"><?=$act->getWorkflowProgressActionStyleInnerButtonLeftHTML()?> <?=$act->getWorkflowProgressActionLabel()?> <?=$act->getWorkflowProgressActionStyleInnerButtonRightHTML()?>
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
				<div class="ccm-notification-actions"><a href="#" data-dismiss-alert="page-alert"><?=t('Hide')?></a></div></div>
			</div>
		<? } ?>
	<? }

	if (!$c->getCollectionPointerID() && (!is_array($workflowList) || count($workflowList) == 0)) {
		if (is_object($vo)) {
			if (!$vo->isApproved() && !$c->isEditMode()) { ?>

			<?
			$buttons = array();
			if ($canApprovePageVersions && !$c->isCheckedOut()) {
				$pk = \Concrete\Core\Permission\Key\PageKey::getByHandle('approve_page_versions');
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

				$buttons[] = '<a href="' . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $c->getCollectionID() . '&ctask=approve-recent' . $token . '" class="btn btn-primary btn-xs">' . $appLabel . '</a>';

			}

			print Loader::helper('concrete/ui')->notify(array(
				'title' => t('Page is Pending Approval.'),
				'message' => t("This page is newer than what appears to visitors on your live site."),
				'type' => 'info',
				'icon' => 'cog',
				'buttons' => $buttons
			))?>

			<? }
		}
	} ?>	

	<? } ?>	
	</div>

<? }