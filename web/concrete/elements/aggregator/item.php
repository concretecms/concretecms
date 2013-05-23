<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<?
$ap = new Permissions($item->getAggregatorObject());
if ($item->canViewAggregatorItem()) { ?>

<div data-aggregator-item-batch-timestamp="<?=$item->getAggregatorItemBatchTimestamp()?>" data-aggregator-item-id="<?=$item->getAggregatorItemID()?>" class="ccm-aggregator-item h<?=$item->getAggregatorItemSlotHeight()?> w<?=$item->getAggregatorItemSlotWidth()?>">
  <div class="ccm-aggregator-item-inner">
  <? if ($showTileCommands && $ap->canEditAggregatorItems()) { ?>
    <ul class="ccm-aggregator-item-inline-commands ccm-ui">
      <li class="ccm-aggregator-item-inline-move"><a data-inline-command="move-tile" href="#"><i class="icon-move"></i></a></li>
      <li class="ccm-aggregator-item-inline-options"><a data-inline-command="options-tile" href="#"><i class="icon-cog"></i></a></li>
    </ul>
  <? } ?>
  <? $item->render(); ?>
</div>
</div>

<? } ?>