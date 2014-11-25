<?
if (is_object($configuration)) {
	$path = $configuration->getRootPath();
	$relativePath = $configuration->getWebRootRelativePath();
}
?>
<? $form = Loader::helper('form'); ?>
<div class="form-group">
    <label for="path"><?=t('Root Path')?></label>
    <div class="input-group">
        <?=$form->text('fslType[path]', $path)?>
        <span class="input-group-addon"><i class="fa fa-asterisk"></i></span>
    </div>
</div>
<div class="form-group">
    <label for="path"><?=t('Relative Path')?></label>
    <?=$form->text('fslType[relativePath]', $relativePath)?>
</div>