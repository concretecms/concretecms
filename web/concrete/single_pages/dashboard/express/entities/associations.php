<?php defined('C5_EXECUTE') or die("Access Denied.");?>

<div class="ccm-dashboard-header-buttons">
    <a href="<?=URL::to('/dashboard/express/entities/associations', 'add', $entity->getId())?>" class="btn btn-primary">
        <?=t("Add Association")?>
    </a>
</div>

<div class="row">
    <? View::element('dashboard/express/detail_navigation', array('entity' => $entity))?>
    <div class="col-md-8">

        <?php if (count($associations)) {
            ?>

            <ul class="item-select-list" id="ccm-stack-list">
                <?php foreach($associations as $association) {
                    $formatter = $association->getFormatter(); ?>

                    <li>
                        <a href="<?=URL::to('/dashboard/express/entities/associations', 'view_association_details', $association->getID())?>">
                            <?=$formatter->getIcon()?> <?=$formatter->getDisplayName()?>
                        </a>
                    </li>
                <? } ?>
            </ul>

        <?php
        } else {
            ?>
            <p><?=t('You have not created any associations.')?></p>
        <?php
        } ?>


    </div>
</div>
