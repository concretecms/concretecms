<?
if (is_object($configuration)) {
	$path = $configuration->getRootPath();
	$relativePath = $configuration->getWebRootRelativePath();
}
?>
<? $form = Loader::helper('form'); ?>
<div class="form-group">
    <label for="path"><?=t('Root Path')?></label>
    <?=$form->text('fslType[path]', $path)?>
</div>
<div class="form-group">
    <label for="path"><?=t('Relative Path')?></label>
    <div class="input-group">
        <?=$form->text('fslType[relativePath]', $relativePath)?>
        <span class="input-group-addon"><i class="glyphicon glyphicon-asterisk"></i></span>
    </div>
</div>