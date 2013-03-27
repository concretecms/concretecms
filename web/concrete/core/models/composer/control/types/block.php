<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_BlockComposerControlType extends ComposerControlType {

	public function getComposerControlObjects() {
		$objects = array();
		$btl = new BlockTypeList();
		$blockTypes = $btl->getBlockTypeList();
		$ci = Loader::helper('concrete/urls');

		foreach($blockTypes as $bt) {
			$bx = new BlockComposerControl();
			$bx->setBlockTypeID($bt->getBlockTypeID());
			$bx->setComposerControlIconSRC($ci->getBlockTypeIconURL($bt));
			$bx->setComposerControlName($bt->getBlockTypeName());
			$objects[] = $bx;
		}
		return $objects;
	}

}