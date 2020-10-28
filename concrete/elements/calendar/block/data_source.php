<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Attribute\Category\SiteCategory;
use Concrete\Core\Calendar\Calendar\CalendarService;
use Concrete\Core\Entity\Calendar\Calendar;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Support\Facade\Application;

/** @var bool $multiple */
/** @var array $caID */
/** @var string $calendarAttributeKeyHandle */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var CalendarService $calendarService */
$calendarService = $app->make(CalendarService::class);
/** @var SiteCategory $siteCategory */
$siteCategory = $app->make(SiteCategory::class);

/** @var Calendar[] $calendars */
$calendars = array_filter(
    $calendarService->getList(),
    function ($calendar) {
        $permissionChecker = new Checker($calendar);
        $responseObject = $permissionChecker->getResponseObject();

        return $responseObject->validate('view_calendar_in_edit_interface');
    }
);

if (isset($multiple) && $multiple) {
    $calendarSelect = [];
} else {
    $calendarSelect = ['' => t('** Select a Calendar')];
}

foreach ($calendars as $calendar) {
    $calendarSelect[$calendar->getID()] = h($calendar->getName());
}

$chooseCalendar = 'all';
$calendarAttributeKeys = [];

$keys = $siteCategory->getList();

foreach ($keys as $ak) {
    if ($ak->getAttributeTypeHandle() == 'calendar') {
        $calendarAttributeKeys[] = $ak;
    }
}

$calendarAttributeKeySelect = ['' => t('** Select Attribute')];
foreach ($calendarAttributeKeys as $ak) {
    $calendarAttributeKeySelect[$ak->getAttributeKeyHandle()] = $ak->getAttributeKeyDisplayName();
}

if ($calendarAttributeKeyHandle) {
    $chooseCalendar = 'site';
} else {
    $chooseCalendar = 'specific';
}
?>

<div class="form-group">
    <?php echo $form->label('chooseCalendar', t('Calendar')); ?>

    <div class="form-check">
        <?php echo $form->radio('chooseCalendar', 'specific', $chooseCalendar, ['id' => 'chooseCalendarSpecific', 'name' => 'chooseCalendar']) ?>
        <?php echo $form->label('chooseCalendarSpecific', t('Specific Calendar'), ['class' => 'form-check-label']) ?>
    </div>

    <?php if (count($calendarAttributeKeys)) { ?>
        <div class="form-check">
            <?php echo $form->radio('chooseCalendar', 'site', $chooseCalendar, ['id' => 'chooseCalendarSite', 'name' => 'chooseCalendar']) ?>
            <?php echo $form->label('chooseCalendarSite', t('Site-wide Calendar'), ['class' => 'form-check-label']) ?>
        </div>

        <div data-row="calendar-attribute">
            <div class="form-group">
                <?php echo $form->label('calendarAttributeKeyHandle', t('Calendar Site Attribute')) ?>
                <?php echo $form->select('calendarAttributeKeyHandle', $calendarAttributeKeySelect, $calendarAttributeKeyHandle) ?>
            </div>
        </div>
    <?php } ?>

    <div data-row="specific-calendar">
        <div class="form-group">
            <?php
            echo $form->label('calendarSelect', t('Calendar'));

            if (isset($multiple) && $multiple) {
                echo $form->selectMultiple('caID[]', $calendarSelect, $caID, ['id' => 'calendarSelect', 'name' => 'caID[]']);
            } else {
                echo $form->select('caID', $calendarSelect, $caID, ['id' => 'calendarSelect', 'name' => 'caID']);
            }
            ?>
        </div>
    </div>
</div>

<!--suppress EqualityComparisonWithCoercionJS -->
<script>
    $(function () {
        <?php if (isset($multiple) && $multiple) { ?>
            $('#caID').selectpicker({
                width: 'fit'
            });
        <?php } ?>

        $('input[name=chooseCalendar]').on('change', function () {
            let selected = $('input[name=chooseCalendar]:checked').val();
            if (selected == 'site') {
                $('div[data-row=calendar-attribute]').show();
                $('div[data-row=specific-calendar]').hide();
            } else if (selected == 'specific') {
                $('div[data-row=specific-calendar]').show();
                $('div[data-row=calendar-attribute]').hide();
            } else {
                $('div[data-row=specific-calendar]').hide();
                $('div[data-row=calendar-attribute]').hide();
            }
        }).trigger('change');
    });
</script>
