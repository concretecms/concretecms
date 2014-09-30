<?
defined('C5_EXECUTE') or die("Access Denied.");

if (count($items)) { ?>

    <? foreach($items as $mi) { ?>

        <div class="ccm-marketplace-list-item-theme">
            <div class="ccm-marketplace-list-item-theme-thumbnail"><?
                $thumb = $mi->getLargeThumbnail();
                printf('<img src="%s">', $thumb->src);
                ?></div>
            <div class="ccm-marketplace-list-item-theme-description">
                <h2><?=$mi->getName()?></h2>
                    <p><?=$mi->getDescription()?></p>
            </div>
            <div class="ccm-marketplace-list-item-theme-price">
                    <?=$mi->getDisplayPrice()?>
            </div>
        </div>

    <? } ?>

    <?=$list->displayPagingV2()?>

<? } else { ?>
    <p><?=t('No results.')?></p>
<? } ?>