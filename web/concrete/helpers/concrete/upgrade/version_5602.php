<?

defined('C5_EXECUTE') or die("Access Denied.");
class ConcreteUpgradeVersion5602Helper {

	public function run() {
		$bt = BlockType::getByHandle('guestbook');
		if (is_object($bt)) {
			$bt->refresh();
		}
	}
		
}
