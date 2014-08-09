<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-block-date-navigation-wrapper">

    <div class="ccm-block-date-navigation-header">
        <h5><?=$title?></h5>
    </div>

    <? if (count($dates)) { ?>
        <ul class="ccm-block-date-navigation-dates">
            <? foreach($dates as $date) { ?>
                <li><a href="<?=$view->controller->getDateLink($date)?>"><?=$view->controller->getDateLabel($date)?></a></li>
            <? } ?>
        </ul>
    <? } else { ?>
        <?=t('None.')?>
    <? } ?>


</div>
