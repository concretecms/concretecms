<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

    <div class="ccm-dashboard-header-buttons">
        <a class="btn btn-primary" href="<?=URL::to('/dashboard/system/multisite/types/add')?>"><?=t("Add Site Type")?></a>
    </div>

<?php if (count($types)) { ?>

    <ul class="item-select-list">
        <?php foreach ($types as $type) {
            ?>
            <li>
                <a href="<?=$view->action('view_type', $type->getSiteTypeID())?>"><i class="fa fa-database"></i> <?=$type->getSiteTypeName()?></a>
            </li>
            <?php
        }
        ?>
    </ul>

<?php } ?>