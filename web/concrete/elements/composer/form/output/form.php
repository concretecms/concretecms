<?
defined('C5_EXECUTE') or die("Access Denied.");
$fieldsets = ComposerFormLayoutSet::getList($composer);
$cmp = new Permissions($composer);
?>

<div class="ccm-composer-save-status"></div>

<div class="alert alert-danger" id="ccm-composer-error-list"></div>

<? foreach($fieldsets as $cfl) { ?>
	<fieldset style="margin-bottom: 0px">
		<? if ($cfl->getComposerFormLayoutSetName()) { ?>
			<legend><?=$cfl->getComposerFormLayoutSetName()?></legend>
		<? } ?>
		<? $controls = ComposerFormLayoutSetControl::getList($cfl);

		foreach($controls as $con) { 
			if (is_object($draft)) { // we are loading content in
				$con->setComposerDraftObject($draft);
			}
			if ($cmp->canAccessComposer($con)) { ?>
				<? $con->render(); ?>
			<? } ?>
			
		<? } ?>

	</fieldset>

<? } ?>

