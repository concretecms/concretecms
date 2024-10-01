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
    <label class="control-label"><?= t('From') ?></label>
    <?= $datetime->datetime('cvPublishDate', $publishDate, true, true, 'light-panel-calendar') ?>
</div>
<div class="form-group form-group-last">
    <label class="control-label"><?= t('To') ?></label>
    <?= $datetime->datetime('cvPublishEndDate', $publishEndDate, true, true, 'light-panel-calendar') ?>
</div>

<div class="text-right">
    <span class="form-text help-block"><?= t('Time Zone: %s', $timezone) ?></span>
</div>

<?php if ($activeVersionExists || $scheduledVersionExists) { ?>
    <div class="form-group">
        <div id="version-scheduling" class="form-check form-switch">
            <?= $form->checkbox('keepOtherScheduling', 1, false, ['id' => 'keepOtherScheduling']) ?>
            <label for="keepOtherScheduling" class="form-check-label">
                <span class="text-standard"><?= $scheduledVersionExists ? t('At the moment, the existing scheduled version will remain.') : t('At the moment, the existing live version will remain.') ?></span>
                <span class="text-active d-none"><?= $scheduledVersionExists ? t('At the moment, current scheduled version gets removed.') : t('At the moment, the current live version gets removed.') ?></span>
            </label>
            <span class="form-text help-block">
                <i class="fa fa-info-circle" aria-hidden="true"></i>
                <span class="info-standard"><?= $scheduledVersionExists ? t('Activate to remove the current scheduled version.') : t('Activate to remove the current live version.') ?></span>
                <span class="info-active d-none"><?= $scheduledVersionExists ? t('Deactivate to leave current scheduled version.') : t('Deactivate to leave current live version online.') ?></span>
            </span>
        </div>
    </div>
<?php } ?>

<div class="dialog-buttons">
    <button type="submit" name="action" value="schedule" class="btn btn-primary ccm-check-in-schedule">
        <?= t('Schedule') ?>
    </button>
</div>

<script>
$(document).ready(function() {
    $('#keepOtherScheduling').change(function() {
        if (this.checked) {
            $('#version-scheduling .text-standard, #version-scheduling .info-standard').addClass('d-none');
            $('#version-scheduling .text-active, #version-scheduling .info-active').removeClass('d-none');
        } else {
            $('#version-scheduling .text-standard, #version-scheduling .info-standard').removeClass('d-none');
            $('#version-scheduling .text-active, #version-scheduling .info-active').addClass('d-none');
        }
    });
});
</script>