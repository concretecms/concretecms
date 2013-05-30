<? defined('C5_EXECUTE') or die("Access Denied.");

$nh = Loader::helper('validation/numbers');
if ($_POST['agID'] && $nh->integer($_POST['agID'])) {
  $aggregator = Aggregator::getByID($_POST['agID']);
  if (is_object($aggregator) && Loader::helper('validation/token')->validate('get_aggregator_items', $_POST['loadToken'])) {
    $showTileCommands = ($_POST['showTileCommands'] && Loader::helper('validation/token')->validate('update_aggregator_items', $_POST['editToken']));
    $list = new AggregatorItemList($aggregator);
    $list->sortByDateDescending();
    $list->setItemsPerPage($_REQUEST['itemsPerPage']);
    $items = $list->getPage($_REQUEST['page']);
    foreach($items as $item) {
      Loader::element('aggregator/tile', array("item" => $item, 'showTileCommands' => $showTileCommands));
    }
  }
}