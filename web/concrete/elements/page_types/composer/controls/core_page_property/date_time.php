<?
defined('C5_EXECUTE') or die("Access Denied.");
$user = Loader::helper('form/user_selector');
?>

<div class="form-group">
	<label class="control-label"><?=$label?></label>
	<? if($description): ?>
	<i class="fa fa-question-circle launch-tooltip" title="" data-original-title="<?=$description?>"></i>
	<? endif; ?>
	<?=Loader::helper('form/date_time')->datetime($this->field('date_time'), $control->getPageTypeComposerControlDraftValue())?>
</div>
