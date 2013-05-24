<? defined('C5_EXECUTE') or die("Access Denied.");

$nh = Loader::helper('validation/numbers');
$form = Loader::helper('form');
$agiID = intval($_REQUEST['agiID']);

$nh = Loader::helper('validation/numbers');
if ($_REQUEST['agiID'] && $nh->integer($_REQUEST['agiID'])) {
  $item = AggregatorItem::getByID($agiID);
  if (is_object($item)) {
    $aggregator = $item->getAggregatorObject();
    $agp = new Permissions($aggregator);
    if ($agp->canEditAggregatorItems()) {
        if (is_object($item) && $_POST['agtID'] && Loader::helper('validation/token')->validate('update_template', $_POST['token'])) {
          $item->setAggregatorItemTemplateID($_POST['agtID']);
          $item->render();
          exit;
        }
        if (is_object($item) && $_POST['task'] == 'delete_item' && Loader::helper('validation/token')->validate('delete_item', $_POST['token'])) {
          $item->delete();
          exit;
        }

        $templates = AggregatorItemTemplate::getList();
        ?>

      <div class="ccm-ui">
        <form data-aggregator-form="template">

        <div class="control-group">
          <?=$form->label('agtID', t('Template'))?>
          <div class="controls">
            <select name="agtID">
              <? foreach($templates as $template) { ?>
                <option <? if (!$item->itemSupportsAggregatorItemTemplate($template)) { ?>disabled<? } ?> value="<?=$template->getAggregatorItemTemplateID()?>" <? if ($template->getAggregatorItemTemplateID() == $item->getAggregatorItemTemplateID()) { ?>selected<? } ?>><?=$template->getAggregatorItemTemplateName()?></option>
              <? } ?>
            </select>
          </div>
        </div>

      </form>

        <div class="dialog-buttons">
          <button class="btn pull-left btn-hover-danger" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
          <button class="btn pull-right btn-primary" onclick="$('form[data-aggregator-form=template]').submit()"><?=t('Save')?></button>
          <button class="btn pull-right btn-danger" onclick="$.fn.ccmaggregator('deleteItem', {agiID: '<?=$agiID?>', 'deleteToken': '<?=Loader::helper('validation/token')->generate('delete_item')?>'})"><?=t('Delete')?></button>
        </div>
      </div>

      <script type="text/javascript">
      $(function() {
        var $form = $('form[data-aggregator-form=template]');
        $form.ccmaggregator('setupTemplateForm', {agiID: '<?=$agiID?>', 'updateToken': '<?=Loader::helper('validation/token')->generate('update_template')?>'});
      });
      </script>

      <? }
    }
}