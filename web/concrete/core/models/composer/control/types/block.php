<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_BlockComposerControlType extends ComposerControlType {

	public function getComposerControlObjects() {
		$objects = array();
		$btl = new BlockTypeList();
		$blockTypes = $btl->getBlockTypeList();
		$ci = Loader::helper('concrete/urls');

		$env = Environment::get();

		foreach($blockTypes as $bt) {
			$cmf = $env->getRecord(DIRNAME_BLOCKS . '/' . $bt->getBlockTypeHandle() . '/' . FILENAME_BLOCK_COMPOSER);
			if ($cmf->exists() || count($bt->getBlockTypeComposerTemplates()) > 0) {
				$bx = new BlockComposerControl();
				$bx->setBlockTypeID($bt->getBlockTypeID());
				$bx->setComposerControlIconSRC($ci->getBlockTypeIconURL($bt));
				$bx->setComposerControlName($bt->getBlockTypeName());
				$objects[] = $bx;
			}
		}
		return $objects;
	}

	public function getComposerControlByIdentifier($identifier) {
		$bt = BlockType::getByID($identifier);
		$ci = Loader::helper('concrete/urls');
		$bx = new BlockComposerControl();
		$bx->setBlockTypeID($bt->getBlockTypeID());
		$bx->setComposerControlIconSRC($ci->getBlockTypeIconURL($bt));
		$bx->setComposerControlName($bt->getBlockTypeName());
		return $bx;
	}

	public function configureFromImport($node) {
		$bt = BlockType::getByHandle((string) $node['handle']);
		return BlockComposerControlType::getComposerControlByIdentifier($bt->getBlockTypeID());
	}
	
	

}