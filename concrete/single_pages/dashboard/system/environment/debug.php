<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Service\UserInterface $interface
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Html\Service\Html $html
 * @var Concrete\Core\Page\View\PageView $view
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var bool $debug_enabled
 * @var bool $show_warnings
 * @var bool $warnings_as_errors
 * @var string $debug_detail
 */

?>

<form method="post" id="debug-form" action="<?= $view->action('update_debug'); ?>">
    <?= $token->output('update_debug'); ?>

    <fieldset>
        <div class="form-group">
            <?= $form->label('', t('Display Errors')); ?>
            <div class="form-check">
                <?= $form->checkbox('debug_enabled', 1, $debug_enabled, ['data-sample' => $view->action('disabled_example')]); ?>
                <?= $form->label('debug_enabled', t('Output error information to site users'), ['class' => 'form-check-label']); ?>
            </div>
            <p class="help-block"><?= t('Disable to show a generic error message'); ?></p>
        </div>

        <div class="form-group">
            <?= $form->label('', t('Error Detail')); ?>
            <div class="form-check">
                <?= $form->radio('debug_detail', 'message', $debug_detail, ['data-sample' => $view->action('message_example')]); ?>
                <?= $form->label('debug_detail1', t('Show the error message but nothing else'), ['class' => 'form-check-label']); ?>
            </div>

            <div class="form-check">
                <?= $form->radio('debug_detail', 'debug', $debug_detail, ['data-sample' => $view->action('debug_example')]); ?>
                <?= $form->label('debug_detail2', t('Show the debug error output'), ['class' => 'form-check-label']); ?>
            </div>
            <p class="help-block">
                <span class="text-danger">
                    <?= t('May disclose sensitive information, use only for development.'); ?>
                </span>
            </p>
        </div>

        <div class="form-group">
            <?= $form->label('', t('Error Level')); ?>
            <div class="form-check">
                <?= $form->checkbox('warnings_as_errors', 1, $warnings_as_errors, ['data-dont-update' => '1']); ?>
                <?= $form->label('warnings_as_errors', t('Consider warnings as errors'), ['class' => 'form-check-label']); ?>
            </div>
            <p class="help-block">
                <span class="text-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?= t('This option should only be enabled by developers: it could brick your site.'); ?><br/>
                    <?= t('In this case you\'ll have to manually delete the %s configuration key.', '<code>concrete.debug.error_reporting</code>'); ?>
                </span>
            </p>
        </div>

        <fieldset>
            <legend><?= t('Example'); ?></legend>
            <iframe id="sample" style="display:none;width:100%;height:600px;border:0"></iframe>
        </fieldset>

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <?= $interface->submit(t('Save'), 'debug-form', 'right', 'btn-primary'); ?>
            </div>
        </div>
    </fieldset>
</form>

<script>
    $(document).ready(function () {

        var form = $('#debug-form'),
            iframe = $('iframe#sample'),
            enabled = form.find('input[name=debug_enabled]'),
            detail = form.find('input[name=debug_detail]'),
            warningsAsErrors = form.find('input[name=warnings_as_errors]'),
            inputs = form.find('input');

        inputs
            .on('change', function () {
                var url;
                if (enabled.is(':checked')) {
                    url = detail.filter(':checked').data('sample');
                } else {
                    url = enabled.data('sample');
                }
                url += (url.indexOf('?') < 0 ? '?' : '&') + 'warnings_as_errors=' + (warningsAsErrors.is(':checked') ? 1 : 0);
                iframe.show().attr('src', url);
            })
            .trigger('change')
        ;

        form.on('submit', function (e) {
            if (warningsAsErrors.is(':checked') && !window.confirm(<?= json_encode(t('Are you sure you want to consider warnings as errors?') . "\n" . t('This option should only be enabled by developers: it could brick your site.')); ?>)) {
                e.preventDefault();
                return false;
            }
        });

    });
</script>
