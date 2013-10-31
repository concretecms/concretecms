<?
defined('C5_EXECUTE') or die("Access Denied.");
$pagetype = $set->getPageTypeObject();
$target = $pagetype->getPageTypePublishTargetObject();
$c = $control->getPageObject();
?>

<div class="form-group">
	<label class="control-label"><?=$label?></label>
	<div data-composer-field="name">
		<?=$target->includeChooseTargetForm($pagetype, $c)?>
	</div>
</div>