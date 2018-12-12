<?php
defined('C5_EXECUTE') or die("Access Denied."); ?>

<form method="post" action="<?php echo $view->action('update_logging'); ?>">
	<?php echo $this->controller->token->output('update_logging'); ?>

    <fieldset>
        <legend><?=t('Mode')?></legend>
        <div class="form-group">
            <div class="radio">
                <label>
                    <?php echo $fh->radio('logging_mode', 'simple', $loggingMode) ?>

                    <span><?php echo t('Simple'); ?></span>
                </label>
            </div>
            <div class="help-block">
                <?=t('Logs everything to the database, accessible via the <a href="%s">Logs Dashboard Page</a>. All custom logging that uses the "application" channel is saved, but any core logs below the threshold level below are discarded.', URL::to('/dashboard/reports/logs'))?>
            </div>

            <div class="form-group">
                <?=$form->label('logging_level', t('Core Logging Level'))?>
                <?=$form->select('logging_level', $levels, $coreLoggingLevel)?>
            </div>
        </div>
        <div class="form-group">
            <div class="radio">
                <label>
                    <?php echo $fh->radio('logging_mode', 'advanced', $loggingMode) ?>

                    <span><?php echo t('Advanced'); ?></span>
                </label>
            </div>

            <div class="help-block">
                <?=t('Loads a custom configuration array from <code>concrete.log.configuration.advanced.configuration</code>. Define your PHP array in the <a href="%s" target="_blank">Monolog Cascade</a> format. <b>Note:</b> unless you specify <code>loggers</code> within your advanced configuration array, siple configuration will be used.', 'https://github.com/theorchard/monolog-cascade/blob/master/README.md')?>
            </div>

        </div>
    </fieldset>

    <fieldset>
        <legend><?=t('Channel Logging')?></legend>
        <div class="form-group">
            <div class="checkbox">
            <label for="ENABLE_LOG_ERRORS">
                <?php echo $fh->checkbox('ENABLE_LOG_ERRORS', 1, $intLogErrors) ?>

                <span><?php echo t('Log Application Exceptions'); ?></span>
            </label>
            </div>
            <div class="checkbox">
            <label for="ENABLE_LOG_EMAILS">
                <?php echo $fh->checkbox('ENABLE_LOG_EMAILS', 1, $intLogEmails) ?>

                <span><?php echo t('Log Emails Sent'); ?></span>
            </label>
            </div>
        </div>
    </fieldset>

    <fieldset>
        <legend><?=t('Reporting')?></legend>
        <div class="form-group">
            <div class="checkbox">
                <label>
                    <?php echo $fh->checkbox('enable_dashboard_report', 1, $enableDashboardReport) ?>

                    <span><?php echo t('Enable Dashboard Logs Report'); ?></span>
                </label>
            </div>
            <div class="help-block">
                <?=t('Enables or disables the Dashboard Logs Page – useful if your logging configuration no longer uses database logging.')?>
            </div>
        </div>
    </fieldset>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
		    <?php echo $interface->submit(t('Save'), 'logging-form', 'right', 'btn-primary'); ?>
        </div>
	</div>
</form>



<script type="text/javascript">
    $(function() {
        $('input[name=logging_mode]').change(function() {
            var $selected = $('input[name=logging_mode]:checked');
            if ($selected.val() == 'simple') {
                $('select[name=logging_level]').prop('disabled', false);
            } else {
                $('select[name=logging_level]').prop('disabled', true);
            }
        });
        $('input[name=logging_mode]:checked').trigger('change');
    });
</script>