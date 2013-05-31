<? defined('C5_EXECUTE') or die("Access Denied.");

$nh = Loader::helper('validation/numbers');
if ($_POST['agID'] && $nh->integer($_POST['agID'])) {
  $aggregator = Aggregator::getByID($_POST['agID']);
  if (is_object($aggregator) && Loader::helper('validation/token')->validate('update_aggregator_items', $_POST['editToken'])) {
    $showTileControls = ($_POST['showTileControls'] && Loader::helper('validation/token')->validate('update_aggregator_items', $_POST['editToken']));
    $item = AggregatorItem::getByID($_POST['newerThan']);
    if (is_object($item) && $item->getAggregatorID() == $aggregator->getAggregatorID()) {
      $aggregator->generateAggregatorItems();

      $list = new AggregatorItemList($aggregator);
      $list->sortByDateAscending();  // we sort by date ascending so we can get the ones closest to the last one we've seen, so in case we have to keep refreshing we can do so.
      $list->setItemsPerPage($_REQUEST['itemsPerPage']);
      $list->filterByPublicDate($item->getAggregatorItemPublicDateTime(), '>');
      $items = $list->getPage();
      foreach($items as $item) {
        Loader::element('aggregator/tile', array("item" => $item, 'showTileControls' => $showTileControls));
      }
    }
  }
}