<?php

defined('C5_EXECUTE') or die("Access Denied.");

$calendarPermissions = new Permissions($calendar);
$preferences = Core::make('Concrete\Core\Calendar\Utility\Preferences');
if (!isset($mode)) {
    $mode = null;
}
if (!isset($month)) {
    $month = null;
}
?>


<div class="ccm-dashboard-header-buttons">
    <div class="btn-group">
        <div class="btn-group">
            <button type="button" id="calendar_button" class="btn btn-secondary dropdown-toggle"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?= h($calendar->getName()) ?>
                <span class="caret"></span>
            </button>
            <div class="dropdown-menu" role="menu" aria-labelledby="calendar_button">
                <?php foreach ($calendars as $cal) {
    $p = new \Permissions($cal);
    if ($p->canViewCalendarInEditInterface()) {
        ?>
                        <a href="<?= URL::to($preferences->getPreferredViewPath(), 'view',
                                $cal->getID()) ?>" class="dropdown-item"><?= h($cal->getName()) ?></a>
                    <?php
    }
    ?>
                    <?php

} ?>
                <?php if ($calendarPermissions->canEditCalendar() || $calendarPermissions->canEditCalendarPermissions()) {
    ?>
                <div class="dropdown-divider"></div>

            <?php if ($calendarPermissions->canEditCalendar()) {
    ?>
                <a href="<?= URL::to('/dashboard/calendar/add', $calendar->getID()) ?>"
                   class="dropdown-item"><?= t("Details") ?></a>
            <?php
}
    ?>
            <?php if ($calendarPermissions->canEditCalendarPermissions()) {
    ?>
                <a href="<?= URL::to('/dashboard/calendar/permissions',
                    $calendar->getID()) ?>" class="dropdown-item"><?= t("Permissions") ?></a>

                    <?php
}
    ?>
                    <?php
} ?>

                    <?php if ($calendarPermissions->canDeleteCalendar()) {
    ?>
                        <div class="dropdown-divider"></div>
                <a href="#" data-dialog="delete-calendar" class="dropdown-item"><span class="text-danger"><?= t(
                                "Delete Calendar") ?></span></a>
            <?php
} ?>
            </div>
        </div>
        <a href="<?= URL::to('/dashboard/calendar/events', 'view',
            $calendar->getID()) ?>" class="btn btn-secondary <?php if ($mode != 'list') {
    ?>active<?php
} ?>"><i class="fas fa-calendar-alt"></i></a>
        <a href="<?= URL::to('/dashboard/calendar/event_list', 'view',
            $calendar->getID()) ?>" class="btn btn-secondary <?php if ($mode == 'list') {
    ?>active<?php
} ?>"><i class="fas fa-list"></i></a>
        <?php if ($calendarPermissions->canAddCalendarEvent()) {
    ?>
            <a class="dialog-launch btn btn-primary" dialog-width="1100" dialog-title="<?= t('Add Event') ?>"
               dialog-height="600"
               href="<?= URL::to('/ccm/calendar/dialogs/event/add?caID=' . $calendar->getID()) ?>"><?= t("Add Event") ?></a>
        <?php
} ?>
    </div>
</div>


<div style="display: none">
    <div id="ccm-dialog-delete-calendar" class="ccm-ui">
        <form method="post" class="form-stacked" action="<?= $view->action('delete_calendar') ?>">
            <?= Loader::helper("validation/token")->output('delete_calendar') ?>
            <input type="hidden" name="caID" value="<?= $calendar->getID() ?>"/>

            <p><?= t('Are you sure? This action cannot be undone.') ?></p>
        </form>
        <div class="dialog-buttons">
            <button class="btn btn-secondary float-start" onclick="jQuery.fn.dialog.closeTop()"><?= t('Cancel') ?></button>
            <button class="btn btn-danger float-end" onclick="$('#ccm-dialog-delete-calendar form').submit()"><?= t(
                    'Delete Calendar') ?></button>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function () {
        $('a[data-dialog=delete-calendar]').on('click', function () {
            jQuery.fn.dialog.open({
                element: '#ccm-dialog-delete-calendar',
                modal: true,
                width: 320,
                title: '<?=t("Delete Calendar")?>',
                height: 'auto'
            });
        });

        $('select#ccm-dashboard-calendar-year-selector').on('change', function () {
            window.location.href = '<?=URL::to('/dashboard/calendar/events', 'view', $calendar->getID())?>/'
                + $(this).val() + '/' + '<?=$month?>';
        });
    });
</script>