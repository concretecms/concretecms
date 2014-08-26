<?
defined('C5_EXECUTE') or die("Access Denied.");
$pagetype = $set->getPageTypeObject();
$target = $pagetype->getPageTypePublishTargetObject();
$c = $control->getPageObject();
?>

<div class="form-group">
	<label class="control-label"><?=$label?></label>
	<? if($description): ?>
	<i class="fa fa-question-circle launch-tooltip" title="" data-original-title="<?=$description?>"></i>
	<? endif; ?>
	<div data-composer-field="name">
		<?=$target->includeChooseTargetForm($pagetype, $c)?>
	</div>
</div>