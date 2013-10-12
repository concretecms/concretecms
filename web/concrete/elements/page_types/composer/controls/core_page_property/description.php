<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="form-group">
	<label class="control-label col-lg-2"><?=$label?></label>
	<div class="col-lg-6">
		<?=$form->textarea($this->field('description'), $control->getPageTypeComposerControlDraftValue(), array('style' => 'height: 100px'))?>
	</div>
</div>
