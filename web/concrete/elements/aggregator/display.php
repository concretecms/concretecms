<? defined('C5_EXECUTE') or die("Access Denied.");

$items = $list->getPage();
$paginator = $list->getPagination();

if (!is_object($c)) {
  $c = Page::getCurrentPage();
}

$pt = $c->getCollectionThemeObject();
$agp = new Permissions($aggregator);
if ($showTileControls && $agp->canEditAggregatorItems()) {
  $showTileControls = true;
} else {
  $showTileControls = false;
}

?>

<div class="ccm-aggregator-wrapper">

<div data-aggregator-id="<?=$aggregator->getAggregatorID()?>" data-aggregator-current-page="1" class="<? if ($showTileControls) { ?>ccm-aggregator-edit<? } else { ?>ccm-aggregator-view<? } ?> ccm-aggregator-grid">
    <? foreach($items as $item) { ?>
      <?=Loader::element('aggregator/tile', array('item' => $item, 'showTileControls' => $showTileControls))?>
    <? } ?>
</div>

<div class="ccm-aggregator-load-more">
  <button class="btn-large btn" data-aggregator-button="aggregator-load-more-items"><?=t('Load More')?></button>
</div>

</div>

<script type="text/javascript">
$(function() {
  $('div[data-aggregator-id=<?=$aggregator->getAggregatorID()?>]').ccmaggregator({
    totalPages: '<?=$paginator->getTotalPages()?>',
    'itemsPerPage': '<?=$itemsPerPage?>', 
    'gutter': <?=$pt->getThemeAggregatorGridItemMargin()?>,
    columnWidth: <?=$pt->getThemeAggregatorGridItemWidth()?>,
    rowHeight: <?=$pt->getThemeAggregatorGridItemHeight()?>,
    agID: <?=$aggregator->getAggregatorID()?>,
    showTileControls: '<?=$showTileControls?>',
    loadToken: '<?=Loader::helper('validation/token')->generate('get_aggregator_items')?>',
    editToken: '<?=Loader::helper('validation/token')->generate('update_aggregator_items')?>',
    titleEditTemplate: '<?=t('Edit Aggregator Template')?>'
  });
});
</script>

<style type="text/css">
<? for ($i = 1; $i <= 8; $i++) { ?>
  div.w<?=$i?> {
    width: <?=(($i * $pt->getThemeAggregatorGridItemWidth()) + ($pt->getThemeAggregatorGridItemMargin() * ($i - 1)))?>px;
  }

  div.h<?=$i?> {
    height: <?=(($i * $pt->getThemeAggregatorGridItemHeight()) + ($pt->getThemeAggregatorGridItemMargin() * ($i - 1)))?>px;
  }
<? } ?>
</style>