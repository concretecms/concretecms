<? defined('C5_EXECUTE') or die("Access Denied.");

$nh = Loader::helper('validation/numbers');
if($_POST['bID'] && $_POST['cID'] && $nh->integer($_POST['bID']) && $nh->integer($_POST['cID'])) {
  $c = Page::getByID($_POST['cID']);
  if (is_object($c) && !$c->isError()) { 
    $a = Area::get($c, $_POST['arHandle']);
    $b = Block::getByID($_POST['bID'],$c, $a);
    if ($b->getBlockTypeHandle() == BLOCK_HANDLE_SCRAPBOOK_PROXY) {
      $cnt = $b->getController();
      $b = Block::getByID($cnt->getOriginalBlockID());
    }

    $bp = new Permissions($b);
    $showTileCommands = $c->isEditMode() && $bp->canEditBlock();
    if ($bp->canViewBlock() && Loader::helper('validation/token')->validate('get_aggregator_items', $_POST['token'])) {
      $controller = $b->getController();
      if ($controller instanceof CoreAggregatorBlockController) {
        $aggregator = $controller->getAggregatorObject();
        $list = new AggregatorItemList($aggregator);
        $list->sortByDateDescending();
        $items = $list->getPage($_REQUEST['page']);
        foreach($items as $item) {
          Loader::element('aggregator/item', array("item" => $item, 'showTileCommands' => $showTileCommands));
        }
      }
    }
  }
}