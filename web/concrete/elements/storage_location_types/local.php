<?php
defined('C5_EXECUTE') or die('Access Denied');
if (is_object($configuration)) {
    $path = $configuration->getRootPath();
    $relativePath = $configuration->getWebRootRelativePath();
}
?>
<?php $form = Loader::helper('form'); ?>
<div class="form-group">
    <label for="path"><?=t('Root Path')?></label>
    <div class="input-group">
        <?=$form->text('fslType[path]', $path, array('placeholder' => t('Example: %ssome/path', $_SERVER['DOCUMENT_ROOT'] . '/')))?>
        <span class="input-group-addon"><i class="fa fa-asterisk"></i></span>
    </div>
</div>
<div class="form-group">
    <label for="path"><?=t('Relative Path')?></label>
    <?=$form->text('fslType[relativePath]', $relativePath, array('placeholder' => t('Example: %ssome/path', '/')))?>
</div>