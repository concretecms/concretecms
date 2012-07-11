<?

defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_GlobalArea extends Area {

	protected $arIsGlobal = 1;
	
	public function display() {
		$c = Page::getCurrentPage();
		parent::getOrCreate($c, $this->arHandle, 1);		
		parent::display($c);
	}
	
	/**
	 * Note that this function does not delete the global area's stack.
	 * You probably want to call the "delete" method of the Stack model instead.
	 */
	public static function deleteByName($arHandle) { 
		$db = Loader::db();
		$r = $db->Execute('select cID from Areas where arHandle = ? and arIsGlobal = 1', array($arHandle));
		while ($row = $r->FetchRow()) {
			$a = Cache::delete('area', $row['cID'] . ':' . $arHandle);
		}
		$db->Execute('delete from Areas where arHandle = ? and arIsGlobal = 1', array($arHandle));
	}
	
}