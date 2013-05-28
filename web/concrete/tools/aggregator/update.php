<? defined('C5_EXECUTE') or die("Access Denied.");

$nh = Loader::helper('validation/numbers');
if ($_POST['agID'] && $nh->integer($_POST['agID'])) {
  $aggregator = Aggregator::getByID($_POST['agID']);
  if (is_object($aggregator) && Loader::helper('validation/token')->validate('update_aggregator_items', $_POST['editToken'])) {
    $agp = new Permissions($aggregator);
    if ($agp->canEditAggregatorItems()) {
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
            $batch = time();
            foreach($_POST['agiID'] as $agiID) {
              $agi = AggregatorItem::getByID($agiID);
              $agi->setAggregatorItemBatchTimestamp($batch);
              $agi->setAggregatorItemBatchDisplayOrder($displayOrder);
              $displayOrder++;
            }
            break;
          case 'move_to_new_aggregator':
            $item = AggregatorItem::getByID($_POST['agiID']);
            $item->moveToNewAggregator($aggregator);
            $il = new AggregatorItemList($aggregator);
            $il->sortByDateDescending();
            $il->setItemsPerPage($_POST['itemsPerPage']);
            $c = Page::getByID($_POST['cID'], 'RECENT');
            if (is_object($c) && !$c->isError()) {
              Loader::element('aggregator/display', array(
                'aggregator' => $aggregator,
                'list' => $il,
                'c' => $c
              ));
            }
            break;
        }
      }
    }
  }
