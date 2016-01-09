<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="ccm-ui">

    <? if (count($frequentPageTypes) || count($otherPageTypes)) { ?>

    <? if (count($frequentPageTypes) && count($otherPageTypes)) { ?>
        <h5><?=t('Commonly Used')?></h5>
    <? } ?>

    <ul class="item-select-list">

        <? foreach($frequentPageTypes as $pt) { ?>
            <li><a dialog-width="640" dialog-title="<?=t('Add %s', $pt->getPageTypeDisplayName())?>" dialog-height="550" class="dialog-launch" href="<?=URL::to('/ccm/system/dialogs/page/add/compose', $pt->getPageTypeID(), $c->getCollectionID())?>"><i class="fa fa-file-o"></i> <?=$pt->getPageTypeDisplayName()?></a></li>
        <? } ?>

        <? if (count($frequentPageTypes) && count($otherPageTypes)) { ?>
            </ul>
            <h5><?=t('Other')?></h5>
            <ul class="item-select-list">
        <? } ?>

        <? foreach($otherPageTypes as $pt) { ?>
            <li><a dialog-width="640" dialog-title="<?=t('Add %s', $pt->getPageTypeDisplayName())?>" dialog-height="550" class="dialog-launch" href="<?=URL::to('/ccm/system/dialogs/page/add/compose', $pt->getPageTypeID(), $c->getCollectionID())?>"><i class="fa fa-file-o"></i> <?=$pt->getPageTypeDisplayName()?></a></li>
        <? } ?>
    </ul>

    <? } else { ?>
        <p><?=t('You do not have access to add any page types beneath the selected page.')?></p>

    <? } ?>
</div>