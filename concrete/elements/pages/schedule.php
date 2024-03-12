<?php

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Form\Service\Widget\DateTime as DateTimeWidget;
use Concrete\Core\Page\Collection\Version\Version;
use Concrete\Core\Support\Facade\Application;

defined('C5_EXECUTE') or die('Access Denied.');

$app = Application::getFacadeApplication();
$appConfig = $app->make(Repository::class);
$liveVersionStatusOnScheduledVersionApproval = (string) $appConfig->get('concrete.misc.live_version_status_on_scheduled_version_approval');

/** @var Form $form */
$form = $app->make('helper/form');
/** @var DateTimeWidget $datetime */
$datetime = $app->make('helper/form/date_time');

$publishDate = '';
$publishEndDate = '';
$activeVersionExists = false;
$scheduledVersionExists = false;
if (isset($page) && is_object($page)) {
    $v = Version::get($page, 'RECENT');
    $publishDate = $v->getPublishDate();
    $publishEndDate = $v->getPublishEndDate();
    $activeVersion = Version::get($page, 'ACTIVE');
    if (!$activeVersion->isError()) {
        $activeVersionExists = true;
    }
    $scheduledVersion = Version::get($page, 'SCHEDULED');
    if (!$scheduledVersion->isError()) {
        $scheduledVersionExists = true;
    }
}

$dateService = $app->make('date');
$timezone = $dateService->getUserTimeZoneID();
$timezone = $dateService->getTimezoneDisplayName($timezone);
?>

<div class="form-group form-group-last">
    <label class="control-label form-label"><?= t('From') ?></label>
    <?= $datetime->datetime(
    'cvPublishDate',
    $publishDate,
    true,
    true,
    'light-panel-calendar'
); ?>
</div>
<div class="form-group form-group-last">
    <label class="control-label form-label"><?= t('To') ?></label>
    <?= $datetime->datetime(
    'cvPublishEndDate',
    $publishEndDate,
    true,
    true,
    'light-panel-calendar'
); ?>
</div>

<div style="text-align: right">
    <span class="form-text help-block"><?= t('Time Zone: %s', $timezone) ?></span>
</div>

<?php if ($activeVersionExists || $scheduledVersionExists) { ?>
    <div class="form-group">
        <div id="version-scheduling" class="form-check form-switch">
        <?= $form->checkbox('keepOtherScheduling', 1, false) ?>
            <label for="keepOtherScheduling" class="form-check-label">
                <span class="text-standard"><?= $scheduledVersionExists ? t('Activate to remove the current scheduled version.') : t('At the moment, the existing live version will remain.') ?></span>
                <span class="text-active"><?= $scheduledVersionExists ? t('Deactivate to leave current scheduled version.') : t('Deactivate to leave current live version online.') ?></span>
            </label>
            <span class="form-text help-block">
                <i class="fa fa-info-circle" aria-hidden="true"></i>
                <span class="info-standard"><?= $scheduledVersionExists ? t('At the moment, the existing scheduled version will remain.') : t('Activate to remove the current live version.') ?></span>
                <span class="info-active"><?= $scheduledVersionExists ? t('At the moment, current scheduled version gets removed.') : t('At the moment, the current live version gets removed.') ?></span>
            </span>
        </div>
    </div>
<?php } ?>

<div class="dialog-buttons">
    <button type="submit" name="action" value="schedule"
            class="btn btn-primary ccm-check-in-schedule">
        <?= t('Schedule') ?>
    </button>
</div>

<style type="text/css">
    #version-scheduling .text-active,
    #version-scheduling .info-active {
        display: none;
    }

    #version-scheduling #keepOtherScheduling:checked ~ .form-check-label .text-standard,
    #version-scheduling #keepOtherScheduling:checked ~ .help-block .info-standard {
        display: none;
    }

    #version-scheduling #keepOtherScheduling:checked ~ .form-check-label .text-active,
    #version-scheduling #keepOtherScheduling:checked ~ .help-block .info-active {
        display: inline;
    }
    div.ui-dialog button.ccm-check-in-schedule {
        float: right;
    }
</style>
