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
        <div class="form-group form-inline">
            <?= $form->checkbox('banEnabled', 1, $banEnabled) ?>
            <?= t(
                'Lock IP after %1$s failed login attempts in %2$s seconds',
                $form->number('allowedAttempts', $allowedAttempts, ['style' => 'width:70px', 'min' => 1]),
                $form->number('attemptsTimeWindow', $attemptsTimeWindow, ['style' => 'width:90px', 'min' => 1])
            ) ?>
        </div>

        <div class="form-group">
            <?= $form->label('banDurationUnlimited', t('Ban Duration'))?>
            <div class="radio">
                <label>
                    <?= $form->radio('banDurationUnlimited', 0, $banDuration ? 0 : 1) ?>
                    <?= t('Ban IP for %s minutes', $form->number('banDuration', $banDuration ?: 300, ['style' => 'width:90px; display: inline-block', 'min' => 1])) ?>
                </label>
            </div>
            <div class="radio">
                <label>
                    <?= $form->radio('banDurationUnlimited', 1, $banDuration ? 0 : 1) ?>
                    <?= t('Ban Forever') ?>
                </label>
            </div>
        </div>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <input type="submit" class="btn btn-primary pull-right" value="<?= t('Save') ?>" />
        </div>
    </div>
</form>
