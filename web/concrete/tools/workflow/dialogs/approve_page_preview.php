<?php 
defined('C5_EXECUTE') or die("Access Denied.");
$wp = WorkflowProgress::getByID($_REQUEST['wpID']);
$ih = Loader::helper('concrete/interface');
$wf = $wp->getWorkflowObject();
$req = $wp->getWorkflowRequestObject();
if ($wp instanceof PageWorkflowProgress) {

	$rvc = Page::getByID($req->getRequestedPageID(), $req->getRequestedVersionID());
	$rvcp = new Permissions($rvc);
	if ($rvcp->canViewPageVersions()) {
		$rv = $rvc->getVersionObject();
		$rvl = Page::getByID($req->getRequestedPageID(), 'ACTIVE');
		$rvr = Page::getByID($req->getRequestedPageID(), 'RECENT');
		$liveCVID = $rvl->getVersionID();
		$recentCVID = $rvr->getVersionID();
		
		$tabs = array(
			array('requested-version', t('Requested Version: %s', $rv->getVersionComments()), true),
			array('live-version', t('Live Version'))
			);
		
		if ($liveCVID != $recentCVID) { 
			$tabs[] = array('recent-version', t('Most Recent Version'));
		}
		?>
		
		<div class="ccm-ui" style="height: 100%">
		
		<?php echo $ih->tabs($tabs); ?>
		
		<div style="display: block; height: 100%" id="ccm-tab-content-requested-version">
			<iframe border="0" id="v<?php echo time()?>r" frameborder="0" height="100%" width="100%" src="<?php echo BASE_URL . DIR_REL?>/<?php echo DISPATCHER_FILENAME?>?cvID=<?php echo $req->getRequestedVersionID()?>&cID=<?php echo $req->getRequestedPageID()?>&vtask=view_versions"></iframe>
		</div>
		
		<div style="display: none; height: 100%" id="ccm-tab-content-live-version">
			<iframe border="0" id="v<?php echo time()?>l" frameborder="0" height="100%" width="100%" src="<?php echo BASE_URL . DIR_REL?>/<?php echo DISPATCHER_FILENAME?>?cvID=<?php echo $liveCVID?>&cID=<?php echo $req->getRequestedPageID()?>&vtask=view_versions"></iframe>
		</div>

	<?php 	if ($liveCVID != $rvr->getVersionID()) { ?>

		<div style="display: none; height: 100%" id="ccm-tab-content-recent-version">
			<iframe border="0" id="v<?php echo time()?>rec" frameborder="0" height="100%" width="100%" src="<?php echo BASE_URL . DIR_REL?>/<?php echo DISPATCHER_FILENAME?>?cvID=<?php echo $recentCVID?>&cID=<?php echo $req->getRequestedPageID()?>&vtask=view_versions"></iframe>
		</div>
		
	<?php  } ?>
		
		</div>
		
		<?php  } ?>
		
	<?php  } ?>