<?php

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Form\Service\Widget\DateTime as DateTimeWidget;
use Concrete\Core\Page\Collection\Version\Version;
use Concrete\Core\Support\Facade\Application;

defined('C5_EXECUTE') or die("Access Denied.");

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make('helper/form');
/** @var DateTimeWidget $datetime */
$datetime = $app->make('helper/form/date_time');

$publishDate = '';
$publishEndDate = '';
$activeVersionExists = false;
$scheduledVersionExists = false;
if (isset($page) && is_object($page)) {
    $v = Version::get($page, "RECENT");
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
    <label class="control-label form-label"><?=t('From')?></label>
    <?= $datetime->datetime('cvPublishDate', $publishDate, true, true,
        'light-panel-calendar'); ?>
</div>
<div class="form-group form-group-last">
    <label class="control-label form-label"><?=t('To')?></label>
    <?= $datetime->datetime('cvPublishEndDate', $publishEndDate, true, true,
        'light-panel-calendar'); ?>
</div>

<div style="text-align: right">
    <span class="form-text help-block"><?=t('Time Zone: %s', $timezone)?></span>
</div>

<?php if ($activeVersionExists || $scheduledVersionExists) {
    if ($scheduledVersionExists) {
        $keepOtherScheduling = t('Keep existing scheduling. This version will go live separately.');
    } else {
        $keepOtherScheduling = t('Keep live version approved.');
    }
    ?>
<div class="form-group">
    <div class="form-check form-switch">
        <?= $form->checkbox('keepOtherScheduling', 1, false) ?>
        <?= $form->label('keepOtherScheduling', $keepOtherScheduling) ?>
    </div>
</div>
<?php } ?>

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
