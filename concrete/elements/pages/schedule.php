<?php
defined('C5_EXECUTE') or die("Access Denied.");
$datetime = loader::helper('form/date_time');

$publishDate = '';
if (isset($page) && is_object($page)) {
    $v = CollectionVersion::get($page, "RECENT");
    $publishDate = $v->getPublishDate();
}

if (Config::get('concrete.misc.user_timezones')) {
    $user = new User();
    $userInfo = $user->getUserInfoObject();
    $timezone = $userInfo->getUserTimezone();
} else {
    $timezone = Config::get('app.timezone');
} ?>

<div class="form-group form-group-last">
    <label class="control-label"><?=t('Date/Time')?></label>
    <?= $datetime->datetime('check-in-scheduler', $publishDate, false, true,
        'dark-panel-calendar'); ?>
    <span class="help-block" style="display: block"><?=t('Time Zone: %s', $timezone)?></span>
</div>
<div class="dialog-buttons">
    <button type="submit" name="action" value="schedule"
            class="btn btn-primary ccm-check-in-schedule">
        <?=t('Schedule')?>
    </button>
</div>

<style type="text/css">
    div.ui-dialog button.ccm-check-in-schedule {
        float: right;
    }
</style>
