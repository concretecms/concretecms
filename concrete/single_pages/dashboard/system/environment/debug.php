<?php defined('C5_EXECUTE') or die("Access Denied.");
$view = View::getInstance();
?>

<form method="post" id="debug-form"
      action="<?php echo $view->url('/dashboard/system/environment/debug', 'update_debug') ?>">
    <?php echo $this->controller->token->output('update_debug') ?>

    <fieldset>
        <legend><?= t('Display Errors') ?></legend>
        <div class="form-group">

            <div class="checkbox">
                <label>
                    <input data-sample='<?= $view->action('disabled_example') ?>' type="checkbox" name="debug_enabled"
                           value="1" <?= $debug_enabled ? 'checked' : '' ?> />
                    <span><?php echo t('Output error information to site users') ?></span>
                    <span class="help-block"><?= t('Disable to show a generic error message') ?></span>
                </label>
            </div>

        </div>
    </fieldset>

    <fieldset>
        <legend><?= t('Error detail') ?></legend>
        <div class="form-group">

            <div class="radio">
                <label>
                    <input data-sample='<?= $view->action('message_example') ?>' type="radio" name="debug_detail"
                           value="message" <?= $debug_detail != 'debug' ? 'checked' : '' ?> />
                    <span><?php echo t('Show the error message but nothing else') ?></span>
                </label>
            </div>
            <div class="radio">
                <label>
                    <input data-sample='<?= $view->action('debug_example') ?>' type="radio" name="debug_detail"
                           value="debug" <?= $debug_detail == 'debug' ? 'checked' : '' ?> />
                    <span><?php echo t('Show the debug error output') ?></span>

                    <p class="help-block">
                        <span class='text-danger'>
                            <?php echo t('May disclose sensitive information, use only for development.') ?>
                        </span>
                    </p>
                </label>
            </div>

        </div>
    </fieldset>

    <fieldset>
        <legend><?= t('Example') ?></legend>
        <iframe class="sample" style="display:none"></iframe>
    </fieldset>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <?php
            echo $interface->submit(t('Save'), 'debug-form', 'right', 'btn-primary');
            ?>
        </div>
    </div>

</form>

<script>
    (function () {
        var iframe = $('.sample'),
            enabled = $('input[name=debug_enabled]'),
            detail = $('input[name=debug_detail]'),
            inputs = $('input');

        inputs.change(function () {
            var url;
            if (enabled.is(':checked')) {
                url = detail.filter(':checked').data('sample');
            } else {
                url = enabled.data('sample');
            }
            iframe.css({
                width: '100%',
                height: 600,
                border: 'none'
            }).show().attr('src', url);
        }).change();
    }());
</script>
