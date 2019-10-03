<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-dashboard-header-buttons btn-group">
    <a href="<?=$view->action('view')?>" class="btn btn-default""><?=t("Back to List")?></span></a>
    <?php if (!$type->isDefault()) { ?>
        <a href="javascript:void(0)" class="btn btn-danger" data-dialog="delete-type" data-dialog-title="<?=t('Delete Site Type')?>"><?=t("Delete Site Type")?></span></a>
    <?php } ?>
</div>


<div class="ccm-dashboard-dialog-wrapper">

    <div class="ccm-ui" data-dialog-wrapper="delete-type">
        <form method="post" action="<?=$view->action('delete_type')?>">
            <?=Loader::helper("validation/token")->output('delete_type')?>
            <input type="hidden" name="id" value="<?=$type->getSiteTypeID()?>">
            <p><?=t('Are you sure you want to delete this site type? This cannot be undone.')?></p>
            <div class="dialog-buttons">
                <button class="btn btn-default pull-left" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
                <button class="btn btn-danger pull-right" onclick="$('div[data-dialog-wrapper=delete-type] form').submit()"><?=t('Delete Site Type')?></button>
            </div>
        </form>
    </div>
</div>

<?php

$type_menu->render();

if (count($sites)) { ?>

    <h4><?=t('Sites')?></h4>

    <ul class="item-select-list">
        <?php foreach ($sites as $site) {
            ?>
            <li>
                <a href="<?=URL::to('/dashboard/system/multisite/sites', 'view_site', $site->getSiteID())?>"><i class="fa fa-link"></i> <?=$site->getSiteName()?></a>
            </li>
            <?php
        }
        ?>
    </ul>

<?php } else { ?>

    <p><?=t('You have not created any sites of this type.')?></p>

<?php } ?>


