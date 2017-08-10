<?php defined('C5_EXECUTE') or die('Access Denied.');
$app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
$form = $app->make('helper/form');
?>
<div class="form-group">
    <?= $form->label('cstFilename', t('External Form to Include')); ?>
    <select name="filename" id="cstFilename" class="form-control">
        <option value="">** <?= t('Select a form'); ?></option>
        <?php foreach ($filenames as $ffilename) {
            $selected = ($ffilename == $filename) ? " selected" : "";
            echo '<option value="' . $ffilename . '"' . $selected . '>' . $file->unfilename($ffilename) . '</option>';
        } ?>
    </select>
</div>
<div class="help-block">
    <p style="word-break: break-all;"><?= t('This is a list of forms found in your external forms directory:')
    . '<br>'
    . DIR_FILES_BLOCK_TYPES_FORMS_EXTERNAL; ?></p>
</div>
