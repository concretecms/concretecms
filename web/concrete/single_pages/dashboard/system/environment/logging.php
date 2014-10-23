<?php
defined('C5_EXECUTE') or die("Access Denied."); ?>

<form method="post" action="<?php echo $view->action('update_logging'); ?>">
	<?php echo $this->controller->token->output('update_logging'); ?>
		
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
        <div class="checkbox">
        <label>
            <?php echo $fh->checkbox('ENABLE_LOG_QUERIES', 1, $intLogQueries) ?>

            <span><?php echo t('Log Database Queries'); ?></span>
        </label>
        </div>
        <div class="checkbox">
        <label>
            <?php echo $fh->checkbox('ENABLE_LOG_QUERIES_CLEAR', 1, $intLogQueriesClear) ?>

            <span><?php echo t('Clear Query Log on Reload'); ?></span>
        </label>
        </div>

    </div>

	<div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
		    <?php echo $interface->submit(t('Save'), 'logging-form', 'right', 'btn-primary'); ?>
        </div>
	</div>
</form>