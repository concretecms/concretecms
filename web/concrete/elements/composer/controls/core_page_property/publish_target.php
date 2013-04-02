<?
defined('C5_EXECUTE') or die("Access Denied.");
$composer = $set->getComposerObject();
$target = $composer->getComposerTargetObject();
?>

<div class="control-group">
	<label class="control-label"><?=$label?></label>
	<div class="controls" data-composer-field="name">
		<?=$target->includeChooseTargetForm($composer)?>
	</div>
</div>