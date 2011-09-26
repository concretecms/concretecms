<?

defined('C5_EXECUTE') or die("Access Denied.");

class GlobalArea extends Area {

	protected $arIsGlobal = 1;
	
	public function display() {
		$c = Page::getCurrentPage();
		parent::getOrCreate($c, $this->arHandle, 1);		
		parent::display($c);
	}
	
}