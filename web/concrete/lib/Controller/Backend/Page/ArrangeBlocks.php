<?
namespace Concrete\Controller\Backend\Page;
use Concrete\Controller\Backend;
class ArrangeBlocks extends UI {

	public function canAccess() {
		return $this->permissions->canEditPageContents();
	}

	public function arrange() {

		$pc = new PageEditResponse();
		$pc->setPage($this->page);
		$e = Loader::helper('validation/error');

		$nvc = $this->page->getVersionToModify();
		$sourceAreaID = intval($_POST['sourceArea']);
		$destinationAreaID = intval($_POST['area']);
		$affectedAreaIDs = array();
		$affectedAreaIDs[] = $sourceAreaID;
		if ($sourceAreaID != $destinationAreaID) {
			$affectedAreaIDs[] = $destinationAreaID;
		}
		if (PERMISSIONS_MODEL == 'advanced') {
			// first, we check to see if we have permissions to edit the area contents for the source area.
			$arHandle = Area::getAreaHandleFromID($sourceAreaID);
			$ar = Area::getOrCreate($nvc, $arHandle);
			$ap = new Permissions($ar);
			if (!$ap->canEditAreaContents()) {
				$e->add(t('You may not arrange the contents of area %s.', $arHandle));
			} else {
				// now we get further in. We check to see if we're dealing with both a source AND a destination area.
				// if so, we check the area permissions for the destination area.
				if ($sourceAreaID != $destinationAreaID) {
					$destAreaHandle = Area::getAreaHandleFromID($destinationAreaID);
					$destArea = Area::getOrCreate($nvc, $destAreaHandle);
					$destAP = new Permissions($destArea);
					if (!$destAP->canEditAreaContents()) {
						$e->add(t('You may not arrange the contents of area %s.', $destAreaHandle));
					} else {
						// we're not done yet. Now we have to check to see whether this user has permission to add
						// a block of this type to the destination area.
						$b = Block::getByID($_REQUEST['block'], $nvc, $arHandle);
						$bt = $b->getBlockTypeObject();
						if (!$destAP->canAddBlock($bt)) {
							$e->add(t('You may not add %s to area %s.', t($bt->getBlockTypeName()), $destAreaHandle));
						}
					}
				}							
			}

			// now, if we get down here we perform the arrangement
			// it will be set to true if we're in simple permissions mode, or if we've passed all the checks
		}

		if (!$e->has()) {		
			$nvc->processArrangement($_POST['area'], $_POST['block'], $_POST['blocks']);
		}



		$pc->setError($e);
		$pc->outputJSON();
		exit;
	}
}

