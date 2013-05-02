<? defined('C5_EXECUTE') or die("Access Denied."); ?>


<?
$a = $b->getBlockAreaObject();
$c = Page::getCurrentPage();
$pt = $c->getCollectionThemeObject();

if ($c->isEditMode()) {
  $bp = new Permissions($b);
  if ($bp->canEditBlock()) { ?>

    <div class="ccm-aggregator-control-bar" id="ccm-aggregator-control-bar-<?=$b->getBlockID()?>-<?=$a->getAreaID()?>"></div>

  <? } ?>

<? } ?>

<div data-aggregator-id="<?=$aggregator->getAggregatorID()?>" class="ccm-aggregator-grid">
  	<? foreach($items as $item) { ?>
    <div class="ccm-aggregator-item h<?=$item->getAggregatorItemSlotHeight()?> w<?=$item->getAggregatorItemSlotWidth()?>"><?
    	$item->render();
    ?></div>
  	<? } ?>
</div>

<script type="text/javascript">
$(function() {
  var $agg = $("div[data-aggregator-id=<?=$aggregator->getAggregatorID()?>]");
  $agg.packery({
    columnWidth: <?=$pt->getThemeAggregatorGridItemWidth()?>,
    rowHeight: <?=$pt->getThemeAggregatorGridItemHeight()?>
  });
  var $itemElements = $($agg.packery('getItemElements'));
  $itemElements.draggable({
    'stop': function() {
      $agg.packery('layout');
    }
  });
  $agg.packery( 'bindUIDraggableEvents', $itemElements );
  $agg.css('opacity', 1);
});
</script>

<style type="text/css">
  div.w1 {
    width: <?=$pt->getThemeAggregatorGridItemWidth()?>px;
  }

  div.h1 {
    height: <?=$pt->getThemeAggregatorGridItemHeight()?>px;
  }

  div.w2 {
    width: <?=2*$pt->getThemeAggregatorGridItemWidth()?>px;
  }

  div.h2 {
    height: <?=2*$pt->getThemeAggregatorGridItemHeight()?>px;
  }

  div.w3 {
    width: <?=3*$pt->getThemeAggregatorGridItemWidth()?>px;
  }

  div.h3 {
    height: <?=3*$pt->getThemeAggregatorGridItemHeight()?>px;
  }

</style>