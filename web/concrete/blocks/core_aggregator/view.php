<? defined('C5_EXECUTE') or die("Access Denied."); ?>


<?
$a = $b->getBlockAreaObject();
$c = Page::getCurrentPage();
$pt = $c->getCollectionThemeObject();
$showTileCommands = false;

if ($c->isEditMode()) {
  $bp = new Permissions($b);
  if ($bp->canEditBlock()) { 
    $showTileCommands = true;
    ?>

    <div class="ccm-aggregator-control-bar"></div>

  <? } ?>

<? } ?>

<div data-aggregator-id="<?=$aggregator->getAggregatorID()?>" data-aggregator-current-page="1" class="<? if ($showTileCommands) { ?>ccm-aggregator-active-tile-commands<? } ?> ccm-aggregator-grid">
    <? foreach($items as $item) { ?>
      <?=Loader::element('aggregator/item', array('item' => $item))?>
    <? } ?>
</div>

<div class="ccm-aggregator-load-more">
  <button class="btn-large btn" data-aggregator-button="aggregator-load-more-items"><?=t('Load More')?></button>
</div>

<script type="text/javascript">
$(function() {
  var $agg = $("div[data-aggregator-id=<?=$aggregator->getAggregatorID()?>]");
  $agg.packery({
    columnWidth: <?=$pt->getThemeAggregatorGridItemWidth()?>,
    rowHeight: <?=$pt->getThemeAggregatorGridItemHeight()?>
  });
  $agg.css('opacity', 1);

  $('button[data-aggregator-button=aggregator-load-more-items]').on('click', function() {
    var $btn = $(this);
    $btn.prop('disabled', true);
    var page = parseInt($('div[data-aggregator-id=<?=$aggregator->getAggregatorID()?>]').attr('data-aggregator-current-page'));
    $.ajax({
      type: 'post',
      url: "<?=Loader::helper('concrete/urls')->getBlockTypeToolsURL($b)?>/load_more",
      data: {
        'task': 'get_aggregator_items',
        'bID': <?=$b->getBlockID()?>,
        'cID': <?=$c->getCollectionID()?>,
        'arHandle': '<?=urlencode($a->getAreaHandle())?>',
        'page': page + 1,
        'token': '<?=Loader::helper('validation/token')->generate('get_aggregator_items')?>'
      },
      success: function(r) {
        var elements = $(r);
        $.each(elements, function(i, obj) {
          $agg.append(obj);
        });
        $agg.packery('appended', elements);
        $btn.prop('disabled', false);
      }
    });
  });

  <? if ($showTileCommands) { ?>
    var $itemElements = $($agg.packery('getItemElements'));
    $itemElements.draggable({
      'handle': 'a[data-inline-command=move-tile]',
    });

    $agg.packery('on', 'dragItemPositioned', function(pkr, item) {
      var data = [
        {'name': 'task', 'value': 'update_display_order'},
        {'name': 'bID', 'value': <?=$b->getBlockID()?>},
        {'name': 'cID', 'value': <?=$c->getCollectionID()?>},
        {'name': 'arHandle', 'value': '<?=urlencode($a->getAreaHandle())?>'},
        {'name': 'token', 'value': '<?=Loader::helper('validation/token')->generate('update_aggregator_item')?>'}
      ];

      var items = [];
      var elements = pkr.getItemElements();
      for (i = 0; i < elements.length; i++) {
        var $obj = $(elements[i]);
        data.push({'name': 'agiID[' + $obj.attr('data-aggregator-item-batch-timestamp') + '][]', 'value': $obj.attr('data-aggregator-item-id')});
      }

      $.ajax({
        type: 'post',
        url: "<?=Loader::helper('concrete/urls')->getBlockTypeToolsURL($b)?>/update",
        data: data
      });
    });

    $agg.packery( 'bindUIDraggableEvents', $itemElements );
    $itemElements.resizable({
      handles: 'se',
      grid: [<?=$pt->getThemeAggregatorGridItemWidth()?>,<?=$pt->getThemeAggregatorGridItemHeight()?>],
      resize: function(e, ui) {
        var $tile = ui.element,
            wx = parseInt($tile.css('width')),
            hx = parseInt($tile.css('height')),
            w = Math.floor(wx / <?=$pt->getThemeAggregatorGridItemWidth()?>),
            h = Math.floor(hx / <?=$pt->getThemeAggregatorGridItemHeight()?>);

        $agg.packery('layout');

        $.ajax({
          type: 'post',
          url: "<?=Loader::helper('concrete/urls')->getBlockTypeToolsURL($b)?>/update",
          data: {
            'task': 'resize',
            'bID': <?=$b->getBlockID()?>,
            'cID': <?=$c->getCollectionID()?>,
            'arHandle': '<?=urlencode($a->getAreaHandle())?>',
            'agiID': $tile.attr('data-aggregator-item-id'),
            'agiSlotWidth': w,
            'agiSlotHeight': h,
            'token': '<?=Loader::helper('validation/token')->generate('update_aggregator_item')?>'
          }
        });
      }
    });
  <? } ?>
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


</style>