<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-dashboard-header-buttons">
    <a class="btn btn-default" href="<?=URL::to('/dashboard/system/multisite/sites')?>"><i class="fa fa-angle-double-left"></i> <?=t('Back to Sites')?></a>
</div>


<?php
foreach($types as $type) {
    $controller = $service->getController($type);
    $formatter = $controller->getFormatter($type);
    ?>

    <div class="ccm-details-panel panel panel-default" data-details-url="<?=$view->url('/dashboard/system/multisite/sites', 'add', $type->getSiteTypeID())?>">
        <div class="panel-body">

            <div class="media">
                <div class="media-left"><?=$formatter->getSiteTypeIconElement()?></div>
                <div class="media-body">
                    <h4 class="media-heading"><?=$type->getSiteTypeName()?></h4>
                    <?=$formatter->getSiteTypeDescription()?>
                </div>
            </div>
        </div>
    </div>


<?php } ?>
