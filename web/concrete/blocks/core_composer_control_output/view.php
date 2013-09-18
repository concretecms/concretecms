<?
	defined('C5_EXECUTE') or die("Access Denied.");
	$control = ComposerOutputControl::getByID($cmpOutputControlID);
	if (is_object($control)) {
		$fls = ComposerFormLayoutSetControl::getByID($control->getComposerFormLayoutSetControlID());
		$cc = $fls->getComposerControlObject();
		if (is_object($cc)) {
		?>
	<div class="ccm-ui">
		<div class="alert alert-info">
			<?=t('The %s composer form element will output its contents here', $cc->getComposerControlName())?>
		</div>
	</div>
	<? } ?>
<? } ?>