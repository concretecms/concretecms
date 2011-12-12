<?

defined('C5_EXECUTE') or die("Access Denied.");

class Newsflow {
	
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
			$r = $fh->getContents(NEWSFLOW_URL . '/' . DISPATCHER_FILENAME . '/?_ccm_view_external=1&cID=' . $cID);
			$obj = NewsflowItem::parseResponse($r);
			return $obj;			
		}
	}
	
	public static function getEditionByPath($cPath) {
		$ni = self::getInstance();
		$cPath = trim($cPath, '/');
		if (!$ni->hasConnectionError()) {
			$fh = Loader::helper('file');
			$r = $fh->getContents(NEWSFLOW_URL . '/' . DISPATCHER_FILENAME . '/' . $cPath . '/-/view_external');
			$obj = NewsflowItem::parseResponse($r);
			return $obj;			
		}
	}
	
	public static function getSlotContents() {
		if (!isset(self::$slots)) {
			$fh = Loader::helper('file');
			$r = $fh->getContents(NEWSFLOW_SLOT_CONTENT_URL);
			self::$slots = NewsflowSlotItem::parseResponse($r);
		}
		return self::$slots;
	}
}

class NewsflowSlotItem {
	
	protected $content;
	public function __construct($content) {
		$this->content = $content;
	}
	public function getContent() {return $this->content;}

	public static function parseResponse($r) {
		$slots = array();
		try {
			// Parse the returned XML file
			$obj = @Loader::helper('json')->decode($r);
			if (is_object($obj)) {
				if (is_object($obj->slots)) {
					foreach($obj->slots as $key => $content) {
						$cn = new NewsflowSlotItem($content);
						$slots[$key] = $cn;
					}
				}
			}
		} catch (Exception $e) {}
		return $slots;

	}
}

class NewsflowItem {
	
	public function getID() {return $this->id;}
	public function getTitle() {return $this->title;}
	public function getContent() {return $this->content;}
	public function getDate() {return $this->date;}
	public function getDescription() {return $this->description;}
	
	public static function parseResponse($r) {
		try {
			// Parse the returned XML file
			$obj = @Loader::helper('json')->decode($r);
			if (is_object($obj)) {
				$mi = new NewsflowItem();
				$mi->title = $obj->title;
				$mi->content = $obj->content;
				$mi->id = $obj->id;
				$mi->description = $obj->description;
				$mi->date = $obj->date;
				return $mi;
			}
		} catch (Exception $e) {
			throw new Exception(t('Unable to parse news response.'));
		}

	}
	
}