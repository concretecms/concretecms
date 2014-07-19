<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="form-group">
	<label class="control-label"><?=$label?></label>
    <?=$ak->render('composer', $this->getPageTypeComposerControlDraftValue())?>
</div>
