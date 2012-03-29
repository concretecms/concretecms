<?
defined('C5_EXECUTE') or die("Access Denied.");
class ConcreteUpgradeVersion552Helper {

	public function run() {
		$bt = BlockType::getByHandle('image');
		if (is_object($bt)) { 
			$bt->refresh();
		}
		$bt = BlockType::getByHandle('form');
		if (is_object($bt)) { 
			$bt->refresh();
		}
	}


	
}