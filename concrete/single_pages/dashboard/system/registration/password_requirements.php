<?php
defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Core\Form\Service\Form $form */
/* @var Concrete\Core\Validation\CSRF\Token $token */
/* @var Concrete\Core\Page\View\PageView $view */

/* @var int $min */
/* @var int $max */
/* @var int $specialCharacters */
/* @var int $upperCase */
/* @var int $lowerCase */
/* @var string $saveAction */
?>

<form action="<?= $saveAction ?>" method="POST">
    <?php $token->output('save_password_requirements') ?>

    <fieldset>
        <legend><?= t('User Password Requirements') ?></legend>
        <div class="form-group">

            <div class="checkbox">
                <label>
                    <?= $form->checkbox('requireSpecialCharacters', '1', $specialCharacters > 0, ["style" => "position:relative; margin-right:10px;"]) ?>
                    <?= t('Require at least %s special characters', $form->number('specialCharacters', $specialCharacters ?: 1,
                        ["style" => "width:70px; display:inline-block;"])) ?>
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <?= $form->checkbox('requireLowerCase', '1', $lowerCase > 0, ["style" => "position:relative; margin-right:10px;"]) ?>
                    <?= t('Require at least %s lowercase characters', $form->number('lowerCase', $lowerCase ?: 1,
                        ["style" => "width:70px; display:inline-block;"])) ?>
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <?= $form->checkbox('requireUpperCase', '1', $upperCase > 0, ["style" => "position:relative; margin-right:10px;"]) ?>
                    <?= t('Require at least %s uppercase characters', $form->number('upperCase', $upperCase ?: 1,
                        ["style" => "width:70px; display:inline-block;"])) ?>
                </label>
            </div>

            <div class="checkbox">
                <label>
                    <?= $form->checkbox('passwordReuse', '1', passwordReuse > 0, ["style" => "position:relative; margin-right:10px;"]) ?>
                    <?= t('Prevent password reuse') ?>
                </label>
                <div class="hidden">
                    <label><?= t('Track previous passwords') ?></label>
                    <?= $form->number('passwordReuse', $passwordReuse ?: 5) ?>
                </div>
            </div>

        </div>
    </fieldset>

    <fieldset>
        <legend><?= t('Custom Regular Expression Requirements') ?></legend>

        <div class="form-group">
            <p>
                <?=t('Type %s in the following box to proceed.', "<code>{$confirmInvalidateString}</code>")?>
            </p>
            <div class="input">
                <?= $form->text('confirmation', [
                    'data-submit' => $invalidateAction
                ]) ?>
                <div class="invalid-feedback text-danger help-text">
                    <?= t('Please type %s to proceed.', "<code>{$confirmInvalidateString}</code>") ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="alert alert-danger">
                <strong><?=t('Warning:')?></strong> <?=t('This action will automatically log out all users (including yourself!)')?>
            </div>
        </div>
    </fieldset>



    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <div class="pull-right">
                <a href="#" class="invalidate-submit btn btn-danger"><?= t('Log out all active users') ?></a>
                <button class="btn btn-primary" type="submit"><?= t('Save') ?></button>
            </div>
        </div>
    </div>
</form>
