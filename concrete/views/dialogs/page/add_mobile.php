<?php
defined('C5_EXECUTE') or die("Access Denied.");

$frequentPageTypes = $frequentPageTypes ?? [];
$otherPageTypes = $otherPageTypes ?? [];
$drafts = $drafts ?? [];
?>

<div class="ccm-ui">

    <?php if (count($frequentPageTypes) || count($otherPageTypes)) {
        ?>

        <?php if (count($frequentPageTypes) && count($otherPageTypes)) {
            ?>
            <h5><?=t('Commonly Used')?></h5>
            <?php
        }
        ?>

        <ul class="item-select-list">

        <?php foreach ($frequentPageTypes as $pt) {
            ?>
            <li>
                <a dialog-width="640"
                   dialog-title="<?=t('Add %s', $pt->getPageTypeDisplayName())?>"
                   dialog-height="550"
                   class="dialog-launch"
                   href="<?=URL::to('/ccm/system/dialogs/page/add/compose', $pt->getPageTypeID(), 0)?>">
                    <i class="fas fa-file"></i> <?=$pt->getPageTypeDisplayName()?></a>
            </li>
            <?php
        }
        ?>

        <?php if (count($frequentPageTypes) && count($otherPageTypes)) {
            ?>
            </ul>
            <h5>
                <a href="#otherPageTypes" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="otherPageTypes">
                    <i class="fas fa-caret-down"></i> <?=t('Other')?></a>
            </h5>
            <ul class="item-select-list collapse" id="otherPageTypes">
            <?php
        }
        ?>

        <?php foreach ($otherPageTypes as $pt) {
            ?>
            <li>
                <a dialog-width="640"
                   dialog-title="<?=t('Add %s', $pt->getPageTypeDisplayName())?>"
                   dialog-height="550"
                   class="dialog-launch"
                   href="<?=URL::to('/ccm/system/dialogs/page/add/compose', $pt->getPageTypeID(), 0)?>">
                    <i class="fas fa-file"></i> <?=$pt->getPageTypeDisplayName()?></a>
            </li>
            <?php
        }
        ?>
        </ul>

        <?php
    } else {
        ?>
        <p><?=t('You do not have access to add any page types beneath the selected page.')?></p>

        <?php
    } ?>
    <?php if (count($drafts)) { ?>
        <hr>
        <h5><?= t('Page Drafts') ?></h5>
        <ul class="item-select-list">
            <?php foreach ($drafts as $dc) { ?>
                <li>
                    <a href="<?= $dc->getCollectionLink() ?>">
                        <i class="fas fa-file"></i>
                        <?php if ($dc->getCollectionName()) {
                            echo $dc->getCollectionName() . ' ' . Core::make('date')->formatDateTime($dc->getCollectionDateAdded(),
                                    false);
                        } else {
                            echo t('(Untitled)') . ' ' . Core::make('date')->formatDateTime($dc->getCollectionDateAdded(),
                                    false);
                        }
                        ?>
                    </a>
                </li>
            <?php } ?>
        </ul>
    <?php } ?>
</div>