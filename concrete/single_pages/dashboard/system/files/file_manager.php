<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var \Concrete\Core\View\View $view
 * @var \Concrete\Core\Validation\CSRF\Token $token
 * @var \Concrete\Core\Form\Service\Form $form
 */
$keepFoldersOnTop = $keepFoldersOnTop ?? false;
?>
<form method="post" action="<?= $view->action('submit') ?>">
    <?= $token->output('save_file_manager_settings') ?>
    <div class="form-group">
        <div class="form-check">
            <?= $form->checkbox('keepFoldersOnTop', 1, $keepFoldersOnTop) ?>
            <?= $form->label('keepFoldersOnTop', t('Keep folders on top when sorting by name')) ?>
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
