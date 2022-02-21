<?php
defined('C5_EXECUTE') or die('Access denied.');

use Concrete\Core\Support\Facade\Application;

$app = Application::getFacadeApplication();
$form = $app->make('helper/form');
$config = $app->make('config');
?>

<div class="alert alert-info">
    <?= t('A site key and secret key must be provided. They can be obtained from the <a href="%s" target="_blank">reCAPTCHA website</a>.', h($config->get('captcha.recaptcha_v3.url.keys_source'))) ?>
</div>

<div class="form-group">
      <?= $form->label('site_key', t('Site Key')) ?>
      <?= $form->text('site_key', $config->get('captcha.recaptcha_v3.site_key')) ?>
</div>

<div class="form-group">
    <?= $form->label('secret_key', t('Secret Key')) ?>
    <?= $form->text('secret_key', $config->get('captcha.recaptcha_v3.secret_key')) ?>
</div>

<div class="form-group">
    <?= $form->label('score', t('Score')) ?>
    <?= $form->number('score', $config->get('captcha.recaptcha_v3.score', '0.5'), ['min' => '0', 'max' => '1', 'step' => '0.01']) ?>
  <div class="help-block"><?= t('1.0 is very likely a good interaction, 0.0 is very likely a bot')?></div>
</div>

<div class="form-group">
    <?= $form->label('position', t('Position of the reCAPTCHA badge')) ?>
    <?= $form->select(
            'position',
            [
                'bottomright' => t('Bottom Right'),
                'bottomleft' => t('Bottom Left'),
                'inline' => t('Inline'),
            ],
            $config->get('captcha.recaptcha_v3.position')
    ) ?>
</div>

<div class="form-group">
    <?= $form->label('', t('Options')) ?>
    <div class="form-check">
        <label class="form-check-label">
            <?= $form->checkbox('log_score', '1', $config->get('captcha.recaptcha_v3.log_score')) ?>
            <?= t('Log failed score interactions') ?>
        </label>
    </div>
    <div class="form-check">
        <label class="form-check-label">
            <?= $form->checkbox('send_ip', '1', (bool) $config->get('captcha.recaptcha_v3.send_ip')) ?>
            <?= t('Send IP') ?>
        </label>
    </div>
    <div class="help-block"><?= t('For extra checks you can send the clients IP address to reCaptcha (this will effect your GDPR compliance).') ?></div>
</div>
