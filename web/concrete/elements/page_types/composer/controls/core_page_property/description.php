<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="form-group">
	<label class="control-label"><?=$label?></label>
	<?=$form->textarea($this->field('description'), $control->getPageTypeComposerControlDraftValue(), array('style' => 'height: 100px'))?>
</div>
