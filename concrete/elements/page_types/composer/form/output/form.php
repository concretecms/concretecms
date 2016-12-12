<?
defined('C5_EXECUTE') or die("Access Denied.");
use \Concrete\Core\Page\Type\Composer\FormLayoutSet as PageTypeComposerFormLayoutSet;
use \Concrete\Core\Page\Type\Composer\FormLayoutSetControl as PageTypeComposerFormLayoutSetControl;
$fieldsets = PageTypeComposerFormLayoutSet::getList($pagetype);
$cmp = new Permissions($pagetype);
// $targetPage comes from renderComposerOutputForm($page, $targetPage); only
// set in dialog page.

$targetParentPageID = 0;
if (is_object($targetPage)) {
    $targetParentPageID = $targetPage->getCollectionID();
}

?>

<div class="ccm-ui">

<div class="alert alert-info" style="display: none" id="ccm-page-type-composer-form-save-status"></div>

    <input type="hidden" name="ptID" value="<?=$pagetype->getPageTypeID()?>" />

<? foreach($fieldsets as $cfl) { ?>
	<fieldset>
		<? if ($cfl->getPageTypeComposerFormLayoutSetDisplayName()) { ?>
			<legend><?=$cfl->getPageTypeComposerFormLayoutSetDisplayName()?></legend>
		<? } ?>
		<? if ($cfl->getPageTypeComposerFormLayoutSetDisplayDescription()) { ?>
			<span class="help-block"><?=$cfl->getPageTypeComposerFormLayoutSetDisplayDescription()?></span>
		<? } ?>
		<? $controls = PageTypeComposerFormLayoutSetControl::getList($cfl);

		foreach($controls as $con) {
			if (is_object($page)) { // we are loading content in
				$con->setPageObject($page);
			}
            $con->setTargetParentPageID($targetParentPageID);
            ?>
			<? $con->render(); ?>
		<? } ?>

	</fieldset>

<? } ?>

</div>