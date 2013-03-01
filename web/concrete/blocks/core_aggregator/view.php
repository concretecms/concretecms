<? defined('C5_EXECUTE') or die("Access Denied."); ?>


<div data-aggregator-id="<?=$aggregator->getAggregatorID()?>" class="ccm-aggregator-grid gridster">
  <ul>
  	<? foreach($items as $item) { ?>
    <li data-row="1" data-col="1" data-sizex="<?=$item->getAggregatorItemSizeX()?>" data-sizey="<?=$item->getAggregatorItemSizeY()?>"><?
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
