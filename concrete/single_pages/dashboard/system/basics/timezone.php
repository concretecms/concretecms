<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>
<form method="POST" id="user-timezone-form" action="<?= $view->action('update') ?>">
    <?php $token->output('update_timezone') ?>

    <div class="form-group">
        <label class="control-label">
            <?php echo t('Default Timezone') ?>
            <span class="launch-tooltip control-label" data-placement="right" title="<?= t(
                'This will control the default timezone that will be used to display date/times.'
            ) ?>"><i class="fa fa-question-circle"></i></span>
        </label>
        <select class="form-control" name="timezone">
            <?php
            foreach ($timezones as $areaName => $namedTimezones) {
                ?>
                <optgroup label="<?= h($areaName) ?>">
                    <?php
                    foreach ($namedTimezones as $tzID => $tzName) {
                        ?>
                        <option value="<?= h($tzID) ?>"<?= strcasecmp($tzID, $timezone) === 0 ? ' selected="selected"' : '' ?>>
                            <?= h($tzName) ?>
                        </option>
                        <?php
                    } ?>
                </optgroup>
                <?php
            }
            ?>
        </select>
        
    </div>

    <div class="form-group">
        <label class="control-label">
            <?php echo t('User-Specific Timezones') ?>
        </label>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="user_timezones" value="1"<?= $user_timezones ? ' checked="checked"' : '' ?> />
                <span class="launch-tooltip control-label" data-placement="right" title="<?= t(
                    'With this setting enabled, users may specify their own time zone in their user profile, and content timestamps will be adjusted accordingly. Without this setting enabled, content timestamps appear in server time.'
                ) ?>"><?php echo t('Enable user defined time zones.') ?></span>
            </label>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label">
            <?php echo t('Server Timezone') ?>
        </label>
        <div>
            <?= t('PHP time zone: %s', h($serverTimezonePHP)) ?>
        </div>
        <div>
            <?= t('Database time zone: %s', h($serverTimezoneDB)) ?>
        </div>
        <div>
            <?= t('Server tests: %s', $dbTimezoneOk ? ('<span class="text-success">'.tc('TimeZone', 'success.').'</span>') : ('<span class="text-danger">'.t('TIMEZONE MISMATCH!').'</span>')) ?>
        </div>
        <?php
        if (!$dbTimezoneOk) {
            ?>
            <div class="alert alert-warning">
                <?= $dbDeltaDescription ?>
            </div>
            <?php
        }
        ?>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <?php echo $interface->submit(t('Save'), 'user-timezone-form', 'right', 'btn-primary'); ?>
        </div>
    </div>

</form>
