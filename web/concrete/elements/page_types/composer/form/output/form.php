<?
defined('C5_EXECUTE') or die("Access Denied.");
$fieldsets = PageTypeComposerFormLayoutSet::getList($pagetype);
$cmp = new Permissions($pagetype);
?>

<div class="ccm-ui">

<div class="alert alert-info" id="ccm-page-type-composer-form-save-status"></div>

<div class="alert alert-danger" id="ccm-page-type-composer-form-error-list"></div>

<? foreach($fieldsets as $cfl) { ?>
	<fieldset style="margin-bottom: 0px">
		<? if ($cfl->getPageTypeComposerFormLayoutSetName()) { ?>
			<legend><?=$cfl->getPageTypeComposerFormLayoutSetName()?></legend>
		<? } ?>
		<? $controls = PageTypeComposerFormLayoutSetControl::getList($cfl);

		foreach($controls as $con) { 
			if (is_object($page)) { // we are loading content in
				$con->setPageObject($page);
			}
			if ($cmp->canComposePageType($con)) { ?>
				<? $con->render(); ?>
			<? } ?>
			
		<? } ?>

	</fieldset>

<? } ?>


</div>