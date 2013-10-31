<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_PageTheme_Concrete extends PageTheme {

	public function registerAssets() {
		$this->providesAsset('css', 'core/frontend/*');
	}

}
