<? defined('C5_EXECUTE') or die("Access Denied.");

// Helpers
$h = Loader::helper('concrete/ui');
$d = Loader::helper('concrete/dashboard');
?>

<form method="post" id="user-timezone-form" action="<?php echo $view->action('update') ?>">

     <?php echo $this->controller->token->output('update_timezone')?>

    <div class="form-group">
        <div class="checkbox">
            <label>
            <input type="checkbox" name="user_timezones" value="1" <?php if ($user_timezones) { ?> checked <?php } ?> />
            <?php echo t('Enable user defined time zones.') ?>
            </label>
        </div>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <? print $interface->submit(t('Save'), 'user-timezone-form', 'right', 'btn-primary'); ?>
        </div>
    </div>

</form>