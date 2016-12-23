<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>
<form method="POST" id="user-timezone-form" action="<?= $view->action('update') ?>">
    <?php $token->output('update_timezone') ?>

    <fieldset>
        <legend><?=t('Server Configuration')?></legend>

        <div class="form-group">
            <label class="control-label">
                <?php echo t('PHP Setting') ?>
            </label>

            <div><?= h($serverTimezonePHP)?></div>
        </div>
        <div class="form-group">
            <label class="control-label">
                <?php echo t('Database Setting') ?>
            </label>

            <div><?= h($serverTimezoneDB)?></div>
        </div>

        <div class="form-group">
            <label class="control-label launch-tooltip" data-placement="right" title="<?= t(
                'These two values must match, otherwise there will be date inconsistencies.'
            ) ?>">
                <?php echo t('Status') ?>
            </label>
            <div>
                <?php if (!$dbTimezoneOk) { ?>
                    <div class="text-warning"><i class="fa fa-warning"></i>
                        <?= $dbDeltaDescription ?><br />
                        <a href="#" id="user-timezone-autofix" class="btn btn-warning"><?=t('Fix PHP timezone')?></a>
                    </div>
                <?php } else { ?>
                    <div class="text-success"><i class="fa fa-check"></i>
                        <?=t('Success. These time zone values match.')?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </fieldset>
    <fieldset>
        <legend><?=t('Settings')?></legend>
        <div class="form-group">
            <label class="control-label launch-tooltip" data-placement="right" title="<?= t(
                'This will control the default timezone that will be used to display date/times.'
            ) ?>">
                <?php echo t('Default Timezone') ?>
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
    </fieldset>


    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <?php echo $interface->submit(t('Save'), 'user-timezone-form', 'right', 'btn-primary'); ?>
        </div>
    </div>

</form>
<?
if (isset($compatibleTimezones) && !empty($compatibleTimezones)) {
    ?>
    <div id="user-timezone-autofix-dialog" style="display: none" title="<?=t('Select time zone')?>">
        <form method="POST" action="<?= $view->action('setSystemTimezone') ?>" class="ccm-ui">
            <?php $token->output('set_system_timezone') ?>
            <div class="form-group">
                <select class="form-control" size="15" name="new-timezone">
                    <?php
                    foreach ($compatibleTimezones as $timezoneID => $timezoneName) {
                        ?><option value="<?=h($timezoneID)?>"><?=h($timezoneName)?></option><?php
                    }
                    ?>
                </select>
            </div>
        </form>
    </div>
    <?php
}
?>
<script>
$(document).ready(function() {
    $('#user-timezone-autofix').on('click', function(e) {
        e.preventDefault();
        var $dlg = $('#user-timezone-autofix-dialog');
        if ($dlg.length === 0) {
            window.alert(<?=json_encode("No PHP compatible time zone is compatible with the database time zone.\nYou should change the database default timezone.")?>);
            return;
        }
        $dlg.dialog({
            modal: true,
            resizable: false,
            buttons: [
            	{
                    text: <?=json_encode(t('Cancel'))?>,
                    click: function() {
                        $dlg.dialog('close');
                    }
            	},
            	{
                    text: <?=json_encode(t('Apply'))?>,
                    click: function() {
                        if (!$dlg.find('[name="new-timezone"]').val()) {
                        	window.alert(<?=json_encode("Please select one of the listed time zones.")?>);
                            return;
                        }
                        $dlg.find('form').submit();
                    }
            	}
            ]
        });
    });
});
</script>