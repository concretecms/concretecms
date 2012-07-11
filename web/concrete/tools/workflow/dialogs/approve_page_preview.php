<?
defined('C5_EXECUTE') or die("Access Denied.");
$wp = WorkflowProgress::getByID($_REQUEST['wpID']);
$ih = Loader::helper('concrete/interface');
$wf = $wp->getWorkflowObject();
$req = $wp->getWorkflowRequestObject();
if ($wp instanceof PageWorkflowProgress) {

if ($wf->canApproveWorkflowProgressObject($wp)) { 
	$rvc = Page::getByID($req->getRequestedPageID(), $req->getRequestedVersionID());
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
	
	<div class="ccm-ui">
	
	<?=$ih->tabs($tabs); ?>
	
	<div style="display: block" id="ccm-tab-content-requested-version">
		<iframe border="0" id="v<?=time()?>r" frameborder="0" height="100%" width="100%" src="<?=BASE_URL . DIR_REL?>/<?=DISPATCHER_FILENAME?>?cvID=<?=$req->getRequestedVersionID()?>&cID=<?=$req->getRequestedPageID()?>&vtask=view_versions" />
	</div>
	
	<div style="display: none" id="ccm-tab-content-live-version">
		<iframe border="0" id="v<?=time()?>l" frameborder="0" height="100%" width="100%" src="<?=BASE_URL . DIR_REL?>/<?=DISPATCHER_FILENAME?>?cvID=<?=$liveCVID?>&cID=<?=$req->getRequestedPageID()?>&vtask=view_versions" />
	</div>

<?	if ($liveCVID != $rvr->getVersionID()) { ?>

	<div style="display: none" id="ccm-tab-content-recent-version">
		<iframe border="0" id="v<?=time()?>rec" frameborder="0" height="100%" width="100%" src="<?=BASE_URL . DIR_REL?>/<?=DISPATCHER_FILENAME?>?cvID=<?=$recentCVID?>&cID=<?=$req->getRequestedPageID()?>&vtask=view_versions" />
	</div>
	
<? } ?>
	
	</div>
	
	<? } ?>
	
<? } ?>