<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-dashboard-header-buttons">
    <div class="btn-group">
        <button type="button" class="btn btn-default" data-toggle="dropdown">
            <?= $calendar->getName() ?>
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu" role="menu">
            <? foreach ($calendars as $cal) { ?>
                <li><a href="<?= $controller->action('view', $cal->getID()) ?>"><?= $cal->getName() ?></a></li>
            <? } ?>
            <li class="divider"></li>
            <li><a href="<?= URL::to('/dashboard/calendar/add') ?>"><?= t("Add Calendar") ?></a></li>

        </ul>
        <a class="dialog-launch btn btn-primary" dialog-width="640" dialog-title="<?=t('Add Event')?>" dialog-height="400"
            href="<?= URL::to('/ccm/system/dialogs/calendar/event/add', $cal->getID())?>"><?= t("Add Event") ?></a>
    </div>
</div>

<div class="btn-group pull-right">
    <a href="<?=$previousLink?>" class="btn btn-sm btn-default"><i class="fa fa-angle-double-left"></i></a>
    <a href="<?=$todayLink?>" class="btn btn-sm btn-default"><?=t('Today')?></i></a>
    <a href="<?=$nextLink?>" class="btn btn-sm btn-default"><i class="fa fa-angle-double-right"></i></a>
</div>

<h2><?= $monthText ?>
    <small><?= $year ?></small>
</h2>

<table class="ccm-dashboard-calendar table table-bordered">
    <thead>
    <tr>
        <td width="14%"><h4><?= t('Sun') ?></h4></td>
        <td width="14%"><h4><?= t('Mon') ?></h4></td>
        <td width="14%"><h4><?= t('Tue') ?></h4></td>
        <td width="14%"><h4><?= t('Wed') ?></h4></td>
        <td width="14%"><h4><?= t('Thu') ?></h4></td>
        <td width="14%"><h4><?= t('Fri') ?></h4></td>
        <td width="14%"><h4><?= t('Sat') ?></h4></td>
    </tr>
    </thead>
    <tbody>
    <tr>
        <?
        $cols = 0;
        $cellCounter = 0;
        $isToday = false;
        Loader::helper('text');
        for ($i = ($firstDayInMonthNum * -1) + 1; $i <= $daysInMonth; $i++) {
            $cellCounter++;
            if ($cols >= 7) {
                echo '</tr><tr>';
                $cols = 0;
            }
            $cols++;
            $isToday = (date('Y') == $year && $month == date('m') && $i == date('j'));
            ?>
            <td class="<? if ($isToday) { ?>ccm-dashboard-calendar-today<? } ?> <? if ($i > 0) { ?>ccm-dashboard-calendar-active-day<? } ?>">
                <div class="ccm-dashboard-calendar-date-wrap">
                <? if ($i > 0) { ?>
                    <div class="ccm-dashboard-calendar-date"><?= $i ?></div>
                <? } else { ?>
                    <div class="ccm-dashboard-calendar-date-inactive">&nbsp;</div>
                <? } ?>
                </div>
            </td>
        <? }
        while ($cols < 7) {
            echo '<td><div class="ccm-dashboard-calendar-date-wrap"><div class="ccm-dashboard-calendar-date-inactive">&nbsp;</div></div></td>';
            $cols++;
        }
        ?>
    </tr>
    </tbody>
</table>

<style type="text/css">
    table.ccm-dashboard-calendar {
        width: 100%;
    }

    table.ccm-dashboard-calendar > thead > tr > td,
    table.ccm-dashboard-calendar {
        border-width: 0px !important;
    }

    table.ccm-dashboard-calendar td {
        min-height: 100px;
    }

    div.ccm-dashboard-calendar-date-wrap {
        height: 110px;
    }
    div.ccm-dashboard-calendar-date {
        text-align: right;
        font-size: 1.1em;
        color: #666;
    }
    td.ccm-dashboard-calendar-today {
        background-color: rgba(91, 192, 222, 0.15);
    }
</style>