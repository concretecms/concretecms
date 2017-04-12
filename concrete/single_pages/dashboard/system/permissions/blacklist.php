<?php
defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Core\Page\View\PageView $view */
/* @var Concrete\Core\Validation\CSRF\Token $token */
/* @var Concrete\Core\Form\Service\Form $form */

/* @var bool $banEnabled */
/* @var int $allowedAttempts */
/* @var int $attemptsTimeWindow */
/* @var int $banDuration */

$view->element('dashboard/system/permissions/blacklist/menu', ['type' => null]);
?>

<form method="post" id="ipblacklist-form" action="<?= $view->action('update_ipblacklist') ?>">
    <?php $token->output('update_ipblacklist') ?>
    <div class="ccm-pane-body">
        <fieldset>
            <legend><?= t('Smart IP Banning')?></legend>

            <div class="form-group form-inline">
                <?= $form->checkbox('banEnabled', 1, $banEnabled) ?> <?= t('Lock IP after') ?>
                <?= $form->number('allowedAttempts', $allowedAttempts, ['style' => 'width:70px', 'min' => 1]) ?>
                <?= t(/*i18n: before we have the number of failed logins, after we have a time duration */'failed login attempts in') ?>
                <?= $form->number('attemptsTimeWindow', $attemptsTimeWindow, ['style' => 'width:90px', 'min' => 1]) ?>
                <?= t('seconds') ?>
            </div>

            <div class="form-inline form-group radio">
                <?= t('Ban IP For') ?>
                <br />
                <label class="radio">
                    <?= $form->radio('banDurationUnlimited', 0, $banDuration ? 0 : 1) ?>
                    <?= $form->number('banDuration', $banDuration ?: 300, ['style' => 'width:90px', 'min' => 1]) ?>
                    <?= t('minutes') ?>
                </label>
                <br />
                <label class="radio">
                    <?= $form->radio('banDurationUnlimited', 1, $banDuration ? 0 : 1) ?> <?= t('Forever') ?>
                </label>
            </div>
        </fieldset>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <input type="submit" class="btn btn-primary pull-right" value="<?= t('Save') ?>" />
        </div>
    </div>
</form>
