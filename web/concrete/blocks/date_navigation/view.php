<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-block-date-navigation-wrapper">

    <div class="ccm-block-date-navigation-header">
        <h5><?=h($title)?></h5>
    </div>

    <? if (count($dates)) { ?>
        <ul class="ccm-block-date-navigation-dates">
            <li><a href="<?=$view->controller->getDateLink()?>"><?=t('All')?></a></li>

            <? foreach($dates as $date) { ?>
                <li><a href="<?=$view->controller->getDateLink($date)?>"
                        <? if ($view->controller->isSelectedDate($date)) { ?>
                            class="ccm-block-date-navigation-date-selected"
                        <? } ?>><?=$view->controller->getDateLabel($date)?></a></li>
            <? } ?>
        </ul>
    <? } else { ?>
        <?=t('None.')?>
    <? } ?>


</div>
