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

    $controller = $b->getController();
    if ($controller instanceof CoreAggregatorBlockController) {
      $ag = $controller->getAggregatorObject();
      $agp = new Permissions($ag);
      if ($agp->canEditAggregatorItems() && Loader::helper('validation/token')->validate('update_aggregator_item', $_POST['token'])) {
        switch($_POST['task']) {
          case 'resize':
            $agi = AggregatorItem::getByID($_POST['agiID']);
            $sw = intval($_POST['agiSlotWidth']);
            $sh = intval($_POST['agiSlotHeight']);
            if (!$sw) {
              $sw = 1;
            }
            if (!$sh) {
              $sh = 1;
            }
            
            $agi->setAggregatorItemSlotWidth($sw);
            $agi->setAggregatorItemSlotHeight($sh);
            break;
          case 'update_display_order':
            $displayOrder = 0;
            foreach($_POST['agiID'] as $batch => $itemIDs) {
              foreach($itemIDs as $agiID) {
                $agi = AggregatorItem::getByID($agiID);
                if ($agi->getAggregatorItemBatchTimestamp() == $batch) {
                  $agi->setAggregatorItemBatchDisplayOrder($displayOrder);
                  $displayOrder++;
                }
              }
            }
            break;
        }
      }
    }
  }
}