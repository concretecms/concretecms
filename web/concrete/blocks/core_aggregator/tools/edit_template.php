<? defined('C5_EXECUTE') or die("Access Denied.");

$nh = Loader::helper('validation/numbers');
$form = Loader::helper('form');
$agiID = intval($_REQUEST['agiID']);

if($_REQUEST['bID'] && $_REQUEST['cID'] && $nh->integer($_REQUEST['bID']) && $nh->integer($_REQUEST['cID'])) {
  $c = Page::getByID($_REQUEST['cID']);
  if (is_object($c) && !$c->isError()) { 
    $a = Area::get($c, $_REQUEST['arHandle']);
    $b = Block::getByID($_REQUEST['bID'],$c, $a);
    if ($b->getBlockTypeHandle() == BLOCK_HANDLE_SCRAPBOOK_PROXY) {
      $cnt = $b->getController();
      $b = Block::getByID($cnt->getOriginalBlockID());
    }

    $controller = $b->getController();
    if ($controller instanceof CoreAggregatorBlockController) {
      $ag = $controller->getAggregatorObject();
      $agp = new Permissions($ag);
      if ($agp->canEditAggregatorItems()) { 
        $item = AggregatorItem::getByID($agiID);
        if (is_object($item) && $_POST['agtID'] && Loader::helper('validation/token')->validate('update_template', $_POST['token'])) {
          $item->setAggregatorItemTemplateID($_POST['agtID']);
          $item->render();
          exit;
        }
        $list = AggregatorItemTemplate::getList();
        $templates = array();
        foreach($list as $template) {
          if (is_object($template)) {
            $templates[$template->getAggregatorItemTemplateID()] = $template->getAggregatorItemTemplateName();
          }
        }
        ?>

      <div class="ccm-ui">
        <form data-aggregator-form="template">

        <div class="control-group">
          <?=$form->label('agtID', t('Template'))?>
          <div class="controls">
            <?=$form->select('agtID', $templates, $item->getAggregatorItemTemplateID());?>
          </div>
        </div>

      </form>

        <div class="dialog-buttons">
          <button class="btn pull-left btn-hover-danger" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
          <button class="btn pull-right btn-primary" onclick="$('form[data-aggregator-form=template]').submit()"><?=t('Save')?></button>
        </div>
      </div>

      <script type="text/javascript">
      $(function() {
        var $form = $('form[data-aggregator-form=template]');
        $form.on('submit', function() {
          jQuery.fn.dialog.showLoader();
          $.ajax({
            type: 'POST',
            data: {
              agtID: $form.find('select[name=agtID]').val(),
              agiID: '<?=$agiID?>',
              cID: '<?=$c->getCollectionID()?>',
              arHandle: '<?=Loader::helper('text')->entities($a->getAreaHandle())?>',
              bID: '<?=$b->getBlockID()?>',
              token: '<?=Loader::helper('validation/token')->generate('update_template')?>'
            },
            url: '<?=Loader::helper('concrete/urls')->getBlockTypeToolsURL($b)?>/edit_template',
            success: function(r) {
              jQuery.fn.dialog.hideLoader();
              // load the newly rendered HTML into the old aggregator item.
              $('[data-aggregator-item-id=<?=$agiID?>]').find('div.ccm-aggregator-item-inner-render').html(r);
              jQuery.fn.dialog.closeTop();
            }
          });
          return false;
        });
      });
      </script>

      <? }
    }
  }
}