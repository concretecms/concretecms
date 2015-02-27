<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php $form = Loader::helper('form'); ?>
<div class="form-group">
    <?=$form->label('cstFilename', t('File to include'))?>
    <select name="filename" id="cstFilename" class="form-control">
        <option value="">** <?=t('Select a form')?></option>
    <?php foreach($filenames as $filename) {
        echo('<option value="' . $filename . '">' . $file->unfilename($filename) . '</option>');
    } ?>
    </select>
</div>
<div class="help-block">
    <p style="word-break: break-all;"><?=t('This is a list of all files found in your external forms directory: %s', DIR_FILES_BLOCK_TYPES_FORMS_EXTERNAL);?></p>
</div>