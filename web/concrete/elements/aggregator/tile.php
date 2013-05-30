<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<?
$ap = new Permissions($item->getAggregatorObject());
$type = AggregatorItemTemplateType::getByHandle('tile');
$types = AggregatorItemTemplateType::getList();
if ($item->canViewAggregatorItem()) { ?>

<div data-block-type-handle="<?=BLOCK_HANDLE_AGGREGATOR_ITEM_PROXY?>" data-aggregator-item-batch-timestamp="<?=$item->getAggregatorItemBatchTimestamp()?>" data-aggregator-item-id="<?=$item->getAggregatorItemID()?>" class="ccm-aggregator-item h<?=$item->getAggregatorItemSlotHeight()?> w<?=$item->getAggregatorItemSlotWidth()?>">
  <div class="ccm-aggregator-item-inner">
  <? if ($showTileControls && $ap->canEditAggregatorItems()) { ?>
    <ul class="ccm-aggregator-item-inline-commands ccm-ui">
      <li class="ccm-aggregator-item-inline-move"><a data-inline-command="move-tile" href="#"><i class="icon-move"></i></a></li>
      <li class="ccm-aggregator-item-inline-options"><a data-inline-command="options-tile" href="#" data-menu="aggregator-menu-<?=$item->getAggregatorItemID()?>"><i class="icon-cog"></i></a></li>
    </ul>
  <? } ?>

    <div class="ccm-ui">

      <div class="popover fade" id="aggregator-menu-<?=$item->getAggregatorItemID()?>">
        <div class="arrow"></div>
        <div class="popover-inner">
        <ul class="dropdown-menu">
          <? foreach($types as $t) { ?>
            <li><a href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/aggregator/item/template?agiID=<?=$item->getAggregatorItemID()?>&agtTypeID=<?=$t->getAggregatorItemTemplateTypeID()?>&token=<?=Loader::helper('validation/token')->generate('edit_aggregator_item_template')?>" class="dialog-launch" dialog-title="<?=t('Edit %s Template', $t->getAggregatorItemTemplateTypeName())?>" dialog-width="660" dialog-height="430" ><?=t('Edit %s Template', $t->getAggregatorItemTemplateTypeName())?></a></li>
          <? } ?>
            <li class="divider"></li>
            <li><a href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/aggregator/item/delete?agiID=<?=$item->getAggregatorItemID()?>&token=<?=Loader::helper('validation/token')->generate('delete_aggregator_item')?>" class="dialog-launch" dialog-title="<?=t('Delete Item')?>" dialog-width="320" dialog-height="160"><?=t('Delete Tile')?></a></li>
        </ul>
        </div>
      </div>

    </div>


  <div class="ccm-aggregator-item-inner-render">
	  <? $item->render($type); ?>
	</div>
</div>
</div>
<? } ?>