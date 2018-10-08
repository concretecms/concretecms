<?php
defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Core\Form\Service\Form $form */
/* @var Concrete\Core\Validation\CSRF\Token $token */
/* @var Concrete\Core\Page\View\PageView $view */

/* @var bool $invalidateOnIPMismatch */
/* @var bool $invalidateOnUserAgentMismatch */
?>
<form action="<?= $view->action('save') ?>" method="POST">
    <?php $token->output('ccm-perm-sec') ?>

    <div class="form-group">
        <?= $form->label('', t('Force users logout')) ?>
        <div class="checkbox">
            <label>
                <?= $form->checkbox('invalidateOnIPMismatch', '1', $invalidateOnIPMismatch) ?>
                <?= t('Force logout when the IP address changes') ?>
            </label>
        </div>
        <div class="checkbox">
            <label>
                <?= $form->checkbox('invalidateOnUserAgentMismatch', '1', $invalidateOnUserAgentMismatch) ?>
                <?= t('Force logout when the user agent changes') ?>
            </label>
        </div>
    </div>

    <div class="form-group">
        <?= $form->label('generator_tag', t('Generator Tag')); ?>
        <div class="checkbox">
            <label>
                <?= $form->checkbox('generator_tag', 1, $generatorTag); ?>
                <?= t('Add a %s tag with the concrete5 version to the site pages.', '<code>' . h('<meta name="generator" content="...">') . '</code>'); ?>
            </label>
        </div>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button class="pull-right btn btn-primary" type="submit"><?= t('Save') ?></button>
        </div>
    </div>

</form>
