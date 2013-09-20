<?
defined('C5_EXECUTE') or die("Access Denied.");
$pagetype = $set->getPageTypeObject();
$target = $pagetype->getPageTypePublishTargetObject();
$draft = $control->getPageTypeComposerDraftObject();
?>

<div class="control-group">
	<label class="control-label"><?=$label?></label>
	<div class="controls" data-composer-field="name">
		<?=$target->includeChooseTargetForm($composer, $draft)?>
	</div>
</div>