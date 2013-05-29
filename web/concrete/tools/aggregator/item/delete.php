<? defined('C5_EXECUTE') or die("Access Denied.");

$nh = Loader::helper('validation/numbers');
$form = Loader::helper('form');
$agiID = intval($_REQUEST['agiID']);

$nh = Loader::helper('validation/numbers');
$item = AggregatorItem::getByID($agiID);
if (is_object($item)) {
  $aggregator = $item->getAggregatorObject();
  $agp = new Permissions($aggregator);
  if ($agp->canEditAggregatorItems() && Loader::helper('validation/token')->validate('delete_aggregator_item', $_REQUEST['token'])) {
    if ($_POST['task'] == 'delete_item') {
      $item->deactivate();
      exit;
    }
    ?>

    <div class="ccm-ui">
      <p><?=t('Are you sure you want to delete this tile?')?></p>
    </div>


    </form>

    <div class="dialog-buttons">
    <button class="btn pull-left btn-hover-danger" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
    <button class="btn pull-right btn-danger" onclick="$.fn.ccmaggregator('deleteItem', {agiID: '<?=$agiID?>', 'deleteToken': '<?=Loader::helper('validation/token')->generate('delete_aggregator_item')?>'})"><?=t('Delete')?></button>
    </div>

    <? }

  }
?>