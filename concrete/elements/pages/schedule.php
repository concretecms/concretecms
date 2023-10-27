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

// activeVersionExists
$activeVersionExistsText = t('At the moment, the existing live version will remain.');
$activeVersionExistsInfo = 'Activate to remove the current live version.';
$alternativeActiveVersionExistsText = t('Deactivate to leave current live version online.');
$alternativeActiveVersionExistsInfo = 'At the moment, the current live version gets removed.';
// scheduledVersionExists
$scheduledVersionExistsText = t('Activate to remove the current scheduled version.');
$scheduledVersionExistsIfo = 'At the moment, the existing scheduled version will remain.';
$alternativeScheduledVersionExistsText = t('Deactivate to leave current scheduled version.');
$alternativeScheduledVersionExistsIfo = 'At the moment, current scheduled version gets removed.';
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

<?php if ($activeVersionExists || $scheduledVersionExists) {
    if($scheduledVersionExists == false) {
        $primaryText = $activeVersionExistsText;
        $primaryInfo = $activeVersionExistsInfo;
        $alternativeText = $alternativeActiveVersionExistsText;
        $alternativeInfo = $alternativeActiveVersionExistsInfo;
    } else {
        $primaryText = $scheduledVersionExistsText;
        $primaryInfo = $scheduledVersionExistsIfo;
        $alternativeText = $alternativeScheduledVersionExistsText;
        $alternativeInfo = $alternativeScheduledVersionExistsIfo;
    }
    ?>
    <div class="form-group">
        <div class="form-check form-switch">
            <?= $form->checkbox('keepOtherScheduling', 1, false) ?>
            <?= $form->label('keepOtherScheduling', $primaryText) ?>
            <?= $form->label('keepOtherScheduling', $alternativeText, ['class' => 'active']) ?>
                <!-- <?= $icon = '<svg class="svg-icon"><use xlink:href="#icon-info" /></svg>' ?> -->
                <span class="form-text help-block"><?= $icon . $primaryInfo ?></span>
                <span class="form-text help-block active"><?= $icon . $alternativeInfo ?></span>
            <style>
                .svg-icon {
                    width: 22.1px;
                    height: 18.1px;
                    vertical-align: -0.15em;
                    fill: #0099ff;
                    overflow: hidden;
                }
                .form-switch label.active,
                .form-switch span.active {
                    display: none;
                }
                .form-switch input[type="checkbox"]:checked ~ label,
                .form-switch input[type="checkbox"]:checked ~ span {
                    display: none;
                }
                .form-switch input[type="checkbox"]:checked ~ label.active,
                .form-switch input[type="checkbox"]:checked ~ span.active {
                    display: inline-block;
                }
            </style>
        </div>
    <?php } ?>

<div class="dialog-buttons">
    <button type="submit" name="action" value="schedule"
            class="btn btn-primary ccm-check-in-schedule">
        <?= t('Schedule') ?>
    </button>
</div>

<style type="text/css">
    div.ui-dialog button.ccm-check-in-schedule {
        float: right;
    }
</style>
