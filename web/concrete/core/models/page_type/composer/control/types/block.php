<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_BlockPageTypeComposerControlType extends PageTypeComposerControlType {

	public function getPageTypeComposerControlObjects() {
		$objects = array();
		$btl = new BlockTypeList();
		$blockTypes = $btl->getBlockTypeList();
		$ci = Loader::helper('concrete/urls');

		$env = Environment::get();

		foreach($blockTypes as $bt) {
			$cmf = $env->getRecord(DIRNAME_BLOCKS . '/' . $bt->getBlockTypeHandle() . '/' . FILENAME_BLOCK_COMPOSER);
			if ($cmf->exists() || count($bt->getBlockTypeComposerTemplates()) > 0) {
				$bx = new BlockPageTypeComposerControl();
				$bx->setBlockTypeID($bt->getBlockTypeID());
				$bx->setPageTypeComposerControlIconSRC($ci->getBlockTypeIconURL($bt));
				$bx->setPageTypeComposerControlName($bt->getBlockTypeName());
				$objects[] = $bx;
			}
		}
		return $objects;
	}

	public function getPageTypeComposerControlByIdentifier($identifier) {
		$bt = BlockType::getByID($identifier);
		$ci = Loader::helper('concrete/urls');
		$bx = new BlockPageTypeComposerControl();
		$bx->setBlockTypeID($bt->getBlockTypeID());
		$bx->setPageTypeComposerControlIconSRC($ci->getBlockTypeIconURL($bt));
		$bx->setPageTypeComposerControlName($bt->getBlockTypeName());
		return $bx;
	}

	public function controlTypeSupportsOutputControl() {return true;}
	
	public function configureFromImport($node) {
		$bt = BlockType::getByHandle((string) $node['handle']);
		return BlockPageTypeComposerControlType::getPageTypeComposerControlByIdentifier($bt->getBlockTypeID());
	}
	
	

}