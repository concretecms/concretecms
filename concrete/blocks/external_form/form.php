<?php
defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Core\File\Service\File $file */
/* @var Concrete\Core\Form\Service\Form $form */

/* @var string[] $filenames */
/* @var string $filename */

$optionValues = [
    '' => '** ' . t('Select a form'),
];
foreach ($filenames as $ffilename) {
    $optionValues[$ffilename] = $file->unfilename($ffilename);
}
?>
<div class="form-group">
    <?= $form->label('cstFilename', t('External Form to Include')) ?>
    <?= $form->select('filename', $optionValues, $filename) ?>
</div>
<div class="help-block">
    <p style="word-break: break-all;"><?= t('This is a list of forms found in your external forms directory:')
    . '<br>'
    . DIR_FILES_BLOCK_TYPES_FORMS_EXTERNAL; ?></p>
</div>
