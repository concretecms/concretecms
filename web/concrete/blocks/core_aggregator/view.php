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
    <div style="width: <?=$item->getAggregatorItemSlotWidth()*$pt->getThemeAggregatorGridItemWidth()?>px; height: <?=$item->getAggregatorItemSlotHeight()*$pt->getThemeAggregatorGridItemHeight()?>px;"><?
    	$item->render();
    ?></div>
  	<? } ?>
</div>

<script type="text/javascript">
$(function() {
  var $agg = $("div[data-aggregator-id=<?=$aggregator->getAggregatorID()?>]");
  $agg.isotope({
  layoutMode: 'perfectMasonry',
    perfectMasonry: {
        columnWidth: <?=$item->getAggregatorItemSlotWidth()?>,
        rowHeight:  <?=$item->getAggregatorItemSlotHeight()?>
    }
  });
  $agg.css('opacity', 1);
});
</script>
