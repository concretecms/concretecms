<?

defined('C5_EXECUTE') or die("Access Denied.");

class GlobalArea extends Area {

	protected $arIsGlobal = 1;
	
	public function display() {
		$c = Page::getByID(HOME_CID, 'RECENT');
		parent::display($c);
	}
	
	public function getOrCreate(&$c, $arHandle) {
		parent::getOrCreate($c, $arHandle, 1);		
	}
	
}