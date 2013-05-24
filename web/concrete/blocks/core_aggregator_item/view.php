<? defined('C5_EXECUTE') or die("Access Denied.");

$a = $b->getBlockAreaObject();
$c = Page::getCurrentPage();
$pt = $c->getCollectionThemeObject();
$showTileCommands = false;
if (is_object($aggregator)) {
	$agp = new Permissions($aggregator);

	if ($c->isEditMode()) {
	  if ($agp->canEditAggregatorItems()) {
	    $showTileCommands = 1;
	  } else {
	    $showTileCommands = 0;
	  }
	} ?>

	<div class="ccm-aggregator-wrapper">

	<div data-aggregator-id="<?=$aggregator->getAggregatorID()?>" data-aggregator-current-page="1" class="<? if ($showTileCommands) { ?>ccm-aggregator-active-tile-commands<? } ?> ccm-aggregator-grid">
	    <? foreach($items as $item) { ?>
	      <?=Loader::element('aggregator/item', array('item' => $item, 'showTileCommands' => $showTileCommands))?>
	    <? } ?>
	</div>

	</div>

	<script type="text/javascript">
	$(function() {
	  $('div[data-aggregator-id=<?=$aggregator->getAggregatorID()?>]').ccmaggregator({
	    totalPages: 1,
	    'itemsPerPage': 1, 
	    columnWidth: <?=$pt->getThemeAggregatorGridItemWidth()?>,
	    rowHeight: <?=$pt->getThemeAggregatorGridItemHeight()?>,
	    agID: <?=$aggregator->getAggregatorID()?>,
	    showTileCommands: '<?=$showTileCommands?>',
	    loadToken: '<?=Loader::helper('validation/token')->generate('get_aggregator_items')?>',
	    editToken: '<?=Loader::helper('validation/token')->generate('update_aggregator_items')?>',
	    titleEditTemplate: '<?=t('Edit Aggregator Template')?>'
	  });
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

	  div.w4 {
	    width: <?=4*$pt->getThemeAggregatorGridItemWidth()?>px;
	  }

	  div.h4 {
	    height: <?=4*$pt->getThemeAggregatorGridItemHeight()?>px;
	  }

	  div.w5 {
	    width: <?=5*$pt->getThemeAggregatorGridItemWidth()?>px;
	  }

	  div.h5 {
	    height: <?=5*$pt->getThemeAggregatorGridItemHeight()?>px;
	  }

	  div.w6 {
	    width: <?=6*$pt->getThemeAggregatorGridItemWidth()?>px;
	  }

	  div.h6 {
	    height: <?=6*$pt->getThemeAggregatorGridItemHeight()?>px;
	  }

	  div.w7 {
	    width: <?=7*$pt->getThemeAggregatorGridItemWidth()?>px;
	  }

	  div.h7 {
	    height: <?=7*$pt->getThemeAggregatorGridItemHeight()?>px;
	  }

	  div.w8 {
	    width: <?=8*$pt->getThemeAggregatorGridItemWidth()?>px;
	  }

	  div.h8 {
	    height: <?=8*$pt->getThemeAggregatorGridItemHeight()?>px;
	  }


	</style>

<? } ?>
