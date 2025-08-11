<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-dashboard-header-buttons">
    <div class="btn-group">

    <button type="button" class="btn btn-secondary dropdown-toggle" data-button="attribute-type" data-bs-toggle="dropdown">
        <?= t('Options') ?> <span class="caret"></span>
    </button>
    <div class="dropdown-menu">
        <?php
        if ($view->controller->getAction() == 'include_unapproved_entities') {
            ?>
            <a class="dropdown-item" href="<?= $view->action('view') ?>"><span class="text-success"><i class="fas fa-check"></i> <?= t('Include Un-Published Entities') ?></span></a>
            <?php
        } else {
            ?>
            <a class="dropdown-item" href="<?= $view->action('include_unpublished_entities') ?>"><?= t('Include Un-Published Entities') ?></a>
            <?php
        }
        ?>
    </div>
    <a href="<?=URL::to('/dashboard/system/express/entities', 'add')?>" class="btn btn-primary"><?=t("Add Object")?></a>
</div>
</div>

<?php if (count($entities)) {
    ?>

    <ul class="item-select-list" id="ccm-stack-list">
        <?php foreach ($entities as $entity) {
    ?>

            <li>
                <a href="<?=URL::to('/dashboard/system/express/entities', 'view_entity', $entity->getID())?>">
                    <i class="fas fa-database"></i> <?=$entity->getEntityDisplayName()?>
                </a>
            </li>
        <?php 
}
    ?>
    </ul>

<?php

} else {
    ?>
    <p><?=t('You have not created any data objects.')?></p>
<?php

} ?>

<?php
if (count($unpublishedEntities) > 0) { ?>

    <hr>

    <div class="small">
        <p class="text-secondary"><?=t2('%s un-published entity hidden from display.', '%s un-published entities hidden from display.', count($unpublishedEntities))?></p>
    </div>

<?php } ?>
