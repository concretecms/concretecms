<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Library_Asset_Assets_Conversation extends AssetGroup {
	
	public function __construct() {
		$this->add('conversation/base');
	//	$this->addAsset('bootstrap');
	}
}