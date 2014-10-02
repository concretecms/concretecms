<?
defined('C5_EXECUTE') or die("Access Denied.");
if ($controller->getTask() == 'view_detail') { ?>


<? } else if (count($items)) { ?>

    <? foreach($items as $mi) { ?>

        <div class="ccm-marketplace-list-item">
            <div class="ccm-marketplace-list-item-add-on-thumbnail"><a href="<?=$this->action('view_detail', $mi->getMarketplaceItemID())?>"><?
                $thumb = $mi->getLargeThumbnail();
                printf('<img src="%s">', $thumb->src);
                ?></a></div>
            <div class="ccm-marketplace-list-item-add-on-description">
                <h2><a href="<?=$this->action('view_detail', $mi->getMarketplaceItemID())?>"><?=$mi->getName()?></a></h2>
                    <p><?=$mi->getDescription()?></p>
            </div>
            <div class="ccm-marketplace-list-item-add-on-price">
                    <?=$mi->getDisplayPrice()?>
            </div>
        </div>

    <? } ?>

    <?=$list->displayPagingV2()?>

<? } else { ?>
   <div class="ccm-marketplace-list-item">
   <div class="ccm-marketplace-list-item-add-on-description">
    <p><?=t('No add-ons found.')?></p>
    </div>
    </div>
<? } ?>