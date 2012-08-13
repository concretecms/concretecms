<?

defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Library_Newsflow {
	
	const E_NEWSFLOW_SUPPORT_MANUALLY_DISABLED = 21;

	protected $isConnected = false;
	protected $connectionError = false;
	static $slots;
	
	
	public static function getInstance() {
		static $instance;
		if (!isset($instance)) {
			$m = __CLASS__;
			$instance = new $m;
		}
		return $instance;
	}
	
	public function __construct() {
		if (defined('ENABLE_APP_NEWS') && ENABLE_APP_NEWS == false) {
			$this->connectionError = Newsflow::E_NEWSFLOW_SUPPORT_MANUALLY_DISABLED;
			return;
		}
	}
	
	public function hasConnectionError() {
		return $this->connectionError != false;
	}
	
	public function getConnectionError() {
		return $this->connectionError;
	}
	
	public static function getEditionByID($cID) {
		$ni = self::getInstance();
		if (!$ni->hasConnectionError()) {
			$fh = Loader::helper('file');
			Loader::library('marketplace');
			$cfToken = Marketplace::getSiteToken();
			$r = $fh->getContents(NEWSFLOW_URL . '/' . DISPATCHER_FILENAME . '/?_ccm_view_external=1&cID=' . $cID . '&cfToken=' . $cfToken);
			$obj = NewsflowItem::parseResponse($r);
			return $obj;			
		}
	}
	
	public static function getEditionByPath($cPath) {
		$ni = self::getInstance();
		$cPath = trim($cPath, '/');
		if (!$ni->hasConnectionError()) {
			$fh = Loader::helper('file');
			Loader::library('marketplace');
			$cfToken = Marketplace::getSiteToken();
			$r = $fh->getContents(NEWSFLOW_URL . '/' . DISPATCHER_FILENAME . '/' . $cPath . '/-/view_external?cfToken=' . $cfToken);
			$obj = NewsflowItem::parseResponse($r);
			return $obj;			
		}
	}
	
	public static function getSlotContents() {
		if (!isset(self::$slots)) {
			$fh = Loader::helper('file');
			Loader::library('marketplace');
			$cfToken = Marketplace::getSiteToken();
			$r = $fh->getContents(NEWSFLOW_SLOT_CONTENT_URL . '?cfToken=' . $cfToken);
			self::$slots = NewsflowSlotItem::parseResponse($r);
		}
		return self::$slots;
	}
}
