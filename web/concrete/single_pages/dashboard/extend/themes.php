<?
defined('C5_EXECUTE') or die("Access Denied.");
if ($controller->getTask() == 'view_detail') { ?>


<? } else if (count($items)) { ?>

    <? foreach($items as $mi) { ?>

        <div class="ccm-marketplace-list-item">
            <div class="ccm-marketplace-list-item-theme-thumbnail"><a href="<?=$this->action('view_detail', $mi->getMarketplaceItemID())?>"><?
                $thumb = $mi->getLargeThumbnail();
                printf('<img src="%s">', $thumb->src);
                ?></a></div>
            <div class="ccm-marketplace-list-item-theme-description">
                <h2><a href="<?=$this->action('view_detail', $mi->getMarketplaceItemID())?>"><?=$mi->getName()?></a></h2>
                    <p><?=$mi->getDescription()?></p>
            </div>
            <div class="ccm-marketplace-list-item-theme-price">
                    <?=$mi->getDisplayPrice()?>
            </div>
        </div>

    <? } ?>

    <?=$list->displayPagingV2()?>

<? } else { ?>
   <div class="ccm-marketplace-list-item">
   <div class="ccm-marketplace-list-item-theme-description">
    <p><?=t('No themes found.')?></p>
    </div>
    </div>
<? } ?>