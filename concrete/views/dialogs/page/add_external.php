<?php
defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Controller\Dialog\Page\AddExternal|Concrete\Controller\Dialog\Page\EditExternal $controller */
/* @var Concrete\Core\View\DialogView $view */

/* @var Concrete\Core\Form\Service\Form $form */
/* @var string $name */
/* @var string $link */
/* @var bool $openInNewWindow */
/* @var bool $isEditingExisting */
?>
<div class="ccm-ui">

    <form class="form-stacked" data-dialog-form="add-external-link" method="post" action="<?= $controller->action('submit') ?>">

        <div class="form-group">
            <?= $form->label('name', t('Name')) ?>
            <?= $form->text('name', $name, ['autofocus' => 'autofocus']) ?>
        </div>

        <div class="form-group">
            <?= $form->label('link', t('URL')) ?>
            <?= $form->text('link', $link) ?>
        </div>

        <div class="form-group">
            <div class="checkbox">
                <label>
                    <?= $form->checkbox('openInNewWindow', '1', $openInNewWindow) ?>
                    <?= t('Open Link in New Window') ?>
                </label>
            </div>
        </div>

        <div class="dialog-buttons">
            <button class="btn btn-default pull-left" data-dialog-action="cancel"><?= t('Cancel') ?></button>
            <button type="button" data-dialog-action="submit" class="btn btn-primary pull-right"><?= $isEditingExisting ? t('Save') : t('Add') ?></button>
        </div>
    </form>

</div>
