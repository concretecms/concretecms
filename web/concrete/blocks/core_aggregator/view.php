<? defined('C5_EXECUTE') or die("Access Denied."); ?>


<?
$a = $b->getBlockAreaObject();
$c = Page::getCurrentPage();
if ($c->isEditMode()) {
  $bp = new Permissions($b);
  if ($bp->canEditBlock()) { ?>

    <div class="ccm-aggregator-control-bar" id="ccm-aggregator-control-bar-<?=$b->getBlockID()?>-<?=$a->getAreaID()?>"></div>

  <? } ?>

<? } ?>

<div data-aggregator-id="<?=$aggregator->getAggregatorID()?>" class="ccm-aggregator-grid gridster">
  <ul>
  	<? foreach($items as $item) { ?>
    <li data-row="1" data-col="1" data-sizex="<?=$item->getAggregatorItemSlotWidth()?>" data-sizey="<?=$item->getAggregatorItemSlotHeight()?>"><?
    	$item->render();
    ?></li>
  	<? } ?>
  </ul>
</div>

<?
$c = Page::getCurrentPage();
$pt = $c->getCollectionThemeObject();
?>

<script type="text/javascript">
  var grid;
  $(function(){
    grid = $("div[data-aggregator-id=<?=$aggregator->getAggregatorID()?>] > ul").gridster({
        widget_margins: [<?=$pt->getThemeAggregatorGridItemMargin()?>, <?=$pt->getThemeAggregatorGridItemMargin()?>],
        widget_base_dimensions: [<?=$pt->getThemeAggregatorGridItemWidth()?>,<?=$pt->getThemeAggregatorGridItemHeight()?>]
    }).data('gridster').disable();
    $("div[data-aggregator-id=<?=$aggregator->getAggregatorID()?>]").css('opacity', 1);
  });

</script>
