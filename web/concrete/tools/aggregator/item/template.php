<? defined('C5_EXECUTE') or die("Access Denied.");

$nh = Loader::helper('validation/numbers');
$form = Loader::helper('form');
$agiID = intval($_REQUEST['agiID']);
$agtTypeID = intval($_REQUEST['agtTypeID']);
$type = AggregatorItemTemplateType::getByID($agtTypeID);
$nh = Loader::helper('validation/numbers');
$item = AggregatorItem::getByID($agiID);
if (is_object($item) && is_object($type)) {
  $aggregator = $item->getAggregatorObject();
  $agp = new Permissions($aggregator);
  if ($agp->canEditAggregatorItems() && Loader::helper('validation/token')->validate('edit_aggregator_item_template', $_REQUEST['token'])) {
    $reloadItemTile = false;
    if ($type->getAggregatorItemTemplateTypeHandle() == 'tile') {
      $reloadItemTile = true;
    }

    if ($_POST['task'] == 'update_item_template') {
      $template = AggregatorItemTemplate::getByID($_POST['agtID']);
      $item->setAggregatorItemTemplate($type, $template);
      if ($reloadItemTile) {
        $item->render($type);
      }
      exit;
    }
  
    $assignments = AggregatorItemFeatureAssignment::getList($item);
    $features = array();
    foreach($assignments as $as) {
      $f = $as->getFeatureObject();
      if (is_object($f)) {
        $features[] = $f;
      }
    }

    $templates = AggregatorItemTemplate::getListByType($type);
    ?>


    <script type="text/javascript">
    $(function() {
      $('#ccm-dialog-aggregator-item-templates .ccm-dialog-icon-item-grid-sets ul a').on('click', function() {
        $('#ccm-dialog-aggregator-item-templates .ccm-overlay-icon-item-grid-list li').hide();
        $('#ccm-dialog-aggregator-item-templates .ccm-overlay-icon-item-grid-list li[data-aggregator-item-template-features~=' + $(this).attr('data-tab') + ']').show();
        $('#ccm-dialog-aggregator-item-templates .ccm-dialog-icon-item-grid-sets ul a').removeClass('active');
        $(this).addClass('active');
        return false;
      });

      $($('#ccm-dialog-aggregator-item-templates .ccm-dialog-icon-item-grid-sets ul a').get(0)).trigger('click');

      $('#ccm-dialog-aggregator-item-templates').closest('.ui-dialog-content').addClass('ui-dialog-content-icon-item-grid');
      $('#ccm-dialog-aggregator-item-templates .ccm-icon-item-grid-search input').focus();
      $('#ccm-dialog-aggregator-item-templates .ccm-icon-item-grid-search input').liveUpdate('ccm-dialog-aggregator-item-templates .ccm-overlay-icon-item-grid-list');
      
      $('#ccm-dialog-aggregator-item-templates .ccm-icon-item-grid-search input').on('keyup', function() {
        if ($(this).val() == '') {
          $('#ccm-dialog-aggregator-item-templates .ccm-dialog-icon-item-grid-sets ul a.active').click();
        }
      });
    });
    </script>

    <div class="ccm-ui ccm-dialog-icon-item-grid" id="ccm-dialog-aggregator-item-templates">

    <div class="ccm-dialog-icon-item-grid-sets">

    <form class="form-inline ccm-icon-item-grid-search">
      <i class="icon-search"></i> <input type="search" />
    </form>

      <ul>
    <?
    foreach($features as $f) { ?>
      <li><a href="#" data-tab="<?=$f->getFeatureHandle()?>"><?=$f->getFeatureName()?></a></li>
    <? } ?> 
    </ul>

    </div>

    <div class="ccm-dialog-icon-item-grid-list-wrapper">
      
      <ul class="ccm-overlay-icon-item-grid-list">

      <? foreach($templates as $t) {
        if (!$item->itemSupportsAggregatorItemTemplate($t)) {
          continue;
        }

        $templateFeatures = $t->getAggregatorItemTemplateFeatureHandles();
        $sets = '';
        foreach($templateFeatures as $tfHandle) {
          $sets .= $tfHandle . ' ';
        }
        $sets = trim($sets);
       
        ?>

        <li data-aggregator-item-template-features="<?=$sets?>">
          <a href="javascript:void(0)" <? if ($item->getAggregatorItemTemplateID($type) == $t->getAggregatorItemTemplateID()) { ?>class="ccm-aggregator-item-template-selected"<? } ?> onclick="$.fn.ccmaggregator('updateItemTemplate', {agiID: '<?=$agiID?>', agtID: '<?=$t->getAggregatorItemTemplateID()?>', agtTypeID: '<?=$agtTypeID?>', reloadItemTile: <? if ($reloadItemTile) { ?>true<? } else { ?>false<? } ?>, 'updateToken': '<?=Loader::helper('validation/token')->generate('edit_aggregator_item_template')?>'})"><p><img src="<?=$t->getAggregatorItemTemplateIconSRC()?>" /><span><?=$t->getAggregatorItemTemplateName()?></span></p></a>
        </li>
        
      <? } ?>

      </ul>


    </div>


    <? }

  }
?>