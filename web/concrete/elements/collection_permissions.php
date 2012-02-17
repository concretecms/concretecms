<?
defined('C5_EXECUTE') or die("Access Denied.");
Loader::model('collection_types');
$dh = Loader::helper('date');
$dt = Loader::helper('form/date_time');
$ph = Loader::helper('concrete/permissions');
if ($cp->canAdminPage()) {
	// if it's composer mode and we have a target, then we hack the permissions collection id
	if (isset($isComposer) && $isComposer) {
		$cd = ComposerPage::getByID($c->getCollectionID());
		if ($cd->isComposerDraft()) {
			if ($cd->getComposerDraftPublishParentID() > 0) {
				if ($cd->getCollectionInheritance() == 'PARENT') {
					$c->cParentID = $cd->getComposerDraftPublishParentID();
					$cpID = $c->getParentPermissionsCollectionID();
					$c->cInheritPermissionsFromCID = $cpID;
				}
			}
		}
	}
}
?>
<div class="ccm-ui">
Page Permissions Dialog
<h2><?=t('Included')?></h2>

<?=$ph->printAddAccessEntityButton()?>
</div>