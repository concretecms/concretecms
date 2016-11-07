<?php defined('C5_EXECUTE') or die("Access Denied.");

// Helpers
$h = Loader::helper('concrete/ui');
$d = Loader::helper('concrete/dashboard');
?>

<form method="post" id="user-timezone-form" action="<?php echo $view->action('update') ?>">

    <?php echo $this->controller->token->output('update_timezone') ?>

    <div class="form-group">
        <label class="control-label">
            <?php echo t('Local User Time') ?>
        </label>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="user_timezones"
                       value="1" <?php if ($user_timezones) {
    ?> checked <?php 
} ?> />
                <span class="launch-tooltip control-label" data-placement="right" title="<?= t(
                    'With this setting enabled, users may specify their own time zone in their user profile, and content timestamps will be adjusted accordingly. Without this setting enabled, content timestamps appear in server time.') ?>"><?php echo t('Enable user defined time zones.') ?></span>
            </label>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label">
            <?php echo t('Server Timezone') ?>
        </label>
        <select class="form-control" name="timezone">
            <?php
            foreach ($timezones as $zone => $tzg) {
                ?>
                <optgroup label="<?= h($zone) ?>">
                    <?php
                    foreach ($tzg as $tz) {
                        ?>
                        <option value="<?= h($tz) ?>"
                            <?= strtolower($tz) === strtolower($timezone) ? 'selected' : '' ?>>
                            <?= h($tz) ?>
                        </option>
                    <?php

                    }
                ?>
                </optgroup>
            <?php

            }
            ?>
        </select>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <?php echo $interface->submit(t('Save'), 'user-timezone-form', 'right', 'btn-primary'); ?>
        </div>
    </div>

</form>
