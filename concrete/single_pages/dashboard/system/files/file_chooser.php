<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var \Concrete\Core\View\View $view
 * @var \Concrete\Core\Validation\CSRF\Token $token
 * @var \Concrete\Core\Form\Service\Form $form
 */

?>
<form method="post" action="<?= $view->action('submit') ?>">
    <?= $token->output('save_file_chooser_settings') ?>
    <div class="form-group">

        <?php echo $form->label('fileChooserDefaultTab', t('Default Tab')) ?>
        <div class="form-check">
            <?= $form->radio('fileChooserDefaultTab', 'file_manager', $fileChooserDefaultTab === 'file_manager', ['required' => 'required', 'id'=>'file_manager_option']) ?>
            <label for="file_manager_option">
                <?= t('File Manager') ?>
            </label>
        </div>

        <div class="form-check">
            <?= $form->radio('fileChooserDefaultTab', 'recent_uploads', $fileChooserDefaultTab === 'recent_uploads', ['required' => 'required', 'id'=>'recent_uploads_option']) ?>
            <label for="recent_uploads_option">
                <?= t('Recent Uploads') ?>
            </label>
        </div>


    </div>
    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <div class="float-end">
                <button type="submit" class="btn btn-primary"><?= t('Save') ?></button>
            </div>
        </div>
    </div>
</form>
