<? defined('C5_EXECUTE') or die("Access Denied."); ?>


<div data-aggregator-id="<?=$aggregator->getAggregatorID()?>" class="ccm-aggregator-grid gridster">
  <ul>
  	<? foreach($items as $item) { ?>
    <li data-row="1" data-col="1" data-sizex="1" data-sizey="1"><?
    	$item->render();
    ?></li>
  	<? } ?>
  </ul>
</div>

<script type="text/javascript">
  var grid;
  $(function(){
    grid = $("div[data-aggregator-id=<?=$aggregator->getAggregatorID()?>] > ul").gridster({
        widget_margins: [10, 10],
        widget_base_dimensions: [220, 220],
        min_cols: 2
    }).data('gridster');
  });

</script>
