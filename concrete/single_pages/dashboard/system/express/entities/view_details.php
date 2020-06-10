<?php

/**
 * @var $entity \Concrete\Core\Entity\Express\Entity
 */

defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-dashboard-header-buttons">

    <?php
    $manage = new \Concrete\Controller\Element\Dashboard\Express\Menu($entity);
    $manage->render();
    ?>

</div>


<div class="row">
    <?php View::element('dashboard/express/detail_navigation', ['entity' => $entity]) ?>
    <div class="col-md-8">
        <div class="mb-4">
            <h3><?= t('Details') ?></h3>

            <h4><?= t('Name') ?></h4>
            <p><?= $entity->getEntityDisplayName() ?></p>

            <h4><?= t('Handle') ?></h4>
            <p><?= $entity->getHandle() ?></p>

            <h4><?= t('Description') ?></h4>
            <p><?= $entity->getEntityDisplayDescription() ?></p>

            <?php if ($owned_by = $entity->getOwnedBy()) { ?>
                <h4><?= t('Owned By') ?></h4>
                <p>
                    <a href="<?= URL::to('/dashboard/system/express/entities', 'view_entity', $owned_by->getID()) ?>">
                        <?= $owned_by->getName() ?>
                    </a>
                </p>
            <?php } ?>

            <?php if (!$entity->usesSeparateSiteResultsBuckets()) { ?>
                <h4><?=t('Total Entries')?></h4>
            <p><?php
                if ($resultsNode) {
                    print $resultsNode->getTotalResultsInFolder();
                }
                ?>
            <?php } ?>


        </div>

        <?php if ($entity->usesSeparateSiteResultsBuckets()) { ?>


            <hr>

            <h3 class="mt-4"><?= t('Results Summary') ?></h3>
            <table class="table">
                <thead>
                <th class="w-100"><?= t('Site') ?></th>
                <th><?= t('Results') ?></th>
                </thead>
                <?php foreach($sites as $site) {
                    ?>

                    <tr>
                        <td><?=$site->getSiteName()?></td>
                        <td style="white-space: nowrap" class="text-center">
                            <?php
                            $siteResultsNode = $resultsNode->getSiteResultsNode($site);
                            if ($siteResultsNode) { ?>
                                <?php echo $siteResultsNode->getTotalResultsInFolder() ;?>
                            <?php } else { ?>
                                <span class="text-warning"><?=t('Unknown')?></span>
                                <i class="launch-tooltip fa fa-info-circle" title="<?=t('Unable to find site results folder. Please rescan folders below.')?>"></i>
                            <?php } ?>
                        </td>
                    </tr>

                <?php } ?>

            </table>

            <form method="post" action="<?=$view->action('rescan_entries')?>">
                <?=$form->hidden('entity_id', $entity->getId())?>
                <?=$token->output('rescan_entries')?>
                <button type="submit" name="rescan" class="btn btn-secondary btn-sm float-right"><?=t('Rescan Entries')?></button>
            </form>

        <?php } ?>


    </div>
</div>
