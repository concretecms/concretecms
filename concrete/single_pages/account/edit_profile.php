<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Attribute\Form\Renderer;
use Concrete\Core\Authentication\AuthenticationType;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\Attribute\Key\UserKey;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\UserInfo;
use Concrete\Core\Validation\CSRF\Token;

/** @var UserKey[]|null $unassignedAttributes */
/** @var Renderer $profileFormRenderer */
/** @var array|null $attributeSets */
/** @var UserInfo $profile */

$app = Application::getFacadeApplication();
/** @var Token $token */
$token = $app->make(Token::class);
/** @var Repository $config */
$config = $app->make(Repository::class);
/** @var Form $form */
$form = $app->make(Form::class);
$displayUserName = $displayUserName ?? true;
?>

<form method="post" action="<?php echo $view->action('save'); ?>" enctype="multipart/form-data">
    <?php $token->output('profile_edit'); ?>

    <fieldset>
        <legend>
            <?php echo t('Basic Information'); ?>
        </legend>

        <?php if ($displayUserName) { ?>
            <div class="mb-3">
                <?php echo $form->label('uName', t('Username')); ?>
                <?php echo $form->text('uName', $profile->getUserName()); ?>
            </div>
        <?php } ?>

        <div class="mb-3">
            <?php echo $form->label('uEmail', t('Email')); ?>
            <?php echo $form->text('uEmail', $profile->getUserEmail()); ?>
        </div>

        <?php if ($config->get('concrete.misc.user_timezones')) { ?>
            <div class="mb-3">
                <?php echo $form->label('uTimezone', t('Time Zone')); ?>
                <?php echo $form->select('uTimezone', $date->getTimezones(), ($profile->getUserTimezone() ? $profile->getUserTimezone() : date_default_timezone_get())); ?>
            </div>
        <?php } ?>

        <?php if (is_array($locales) && count($locales)) { ?>
            <div class="mb-3">
                <?php echo $form->label('uDefaultLanguage', t('Language')); ?>
                <?php echo $form->select('uDefaultLanguage', $locales, Localization::activeLocale()); ?>
            </div>
        <?php } ?>
    </fieldset>

    <?php foreach ($attributeSets as $setName => $attributeKeys) { ?>
        <?php /** @var UserKey $attributeKeys */ ?>
        <fieldset>
            <legend>
                <?php echo $setName; ?>
            </legend>

            <?php  foreach ($attributeKeys as $attributeKey) { ?>
                <?php /** @noinspection PhpUndefinedMethodInspection */
                $profileFormRenderer->buildView($attributeKey)->setIsRequired($attributeKey->isAttributeKeyRequiredOnProfile())->render(); ?>
            <?php } ?>
        </fieldset>
    <?php } ?>

    <?php if (!empty($unassignedAttributes)) { ?>
        <fieldset>
            <legend>
                <?php echo t('Other'); ?>
            </legend>

            <?php foreach ($unassignedAttributes as $attributeKey) { ?>
                <?php /** @noinspection PhpUndefinedMethodInspection */
                $profileFormRenderer->buildView($attributeKey)->setIsRequired($attributeKey->isAttributeKeyRequiredOnProfile())->render(); ?>
            <?php } ?>
        </fieldset>
    <?php } ?>

    <?php
    $authenticationTypes = [];

    foreach (AuthenticationType::getList(true, true) as $authenticationType) {
        if ($authenticationType->isHooked($profile)) {
            if ($authenticationType->hasHooked()) {
                $authenticationTypes[] = [$authenticationType, 'renderHooked'];
            }
        } else {
            if ($authenticationType->hasHook()) {
                $authenticationTypes[] = [$authenticationType, 'renderHook'];
            }
        }
    }
    ?>

    <?php if (!empty($authenticationTypes)) { ?>
        <fieldset>
            <legend>
                <?php echo t('Authentication Types'); ?>
            </legend>

            <?php foreach ($authenticationTypes as $authenticationType) { ?>
                <?php call_user_func($authenticationType); ?>
            <?php } ?>
        </fieldset>
    <?php } ?>

    <br/>

    <fieldset>
        <legend>
            <?php echo t('Change Password'); ?>
        </legend>

        <div class="mb-3">
            <?php echo $form->label('uPasswordCurrent', t('Current Password')); ?>
            <?php echo $form->password('uPasswordCurrent', ['autocomplete' => 'off']); ?>
        </div>

        <div class="mb-3">
            <?php echo $form->label('uPasswordNew', t('New Password')); ?>
            <?php echo $form->password('uPasswordNew', ['autocomplete' => 'off']); ?>
        </div>

        <div class="mb-3">
            <?php echo $form->label('uPasswordNewConfirm', t('Confirm New Password')); ?>
            <?php echo $form->password('uPasswordNewConfirm', ['autocomplete' => 'off']); ?>
        </div>
        <div class="help-block"><?php echo h(t('Leave blank to keep current password.')); ?></div>
    </fieldset>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <input type="submit" name="save" value="<?php echo h(t('Save')); ?>" class="btn btn-primary float-end"/>
        </div>
    </div>
</form>
