<?
defined('C5_EXECUTE') or die(_("Access Denied."));

	class AttributeTypeController extends Controller {
		
		protected $identifier;
		protected static $sets = array();
	 	protected $attributeKey;
	 	
	 	public function setAttributeKey($attributeKey) {
	 		$this->attributeKey = $attributeKey;
	 	}
	 	
	 	public function setAttributeValue($attributeValue) {
	 		$this->attributeValue = $attributeValue;
	 	}
	 	
	 	public function getAttributeKey() {
	 		return $this->attributeKey;
	 	}

	 	public function getAttributeValue() {
	 		return $this->attributeValue;
	 	}
	 	
	 	protected function getAttributeValueID() {
	 		return $this->attributeValue->getAttributeValueID();
	 	}
	 	
		public function set($key, $value) {
			AttributeTypeController::$sets[$this->identifier][$key] = $value;		
		}

		public function field($fieldName) {
			return 'akID[' . $this->attributeKey->getAttributeKeyID() . '][' . $fieldName . ']';
		}

		public function getSets() {
			return AttributeTypeController::$sets[$this->identifier];		
		}

		public function __construct($attributeType) {
			$this->identifier = $attributeType->getAttributeTypeID();
			parent::__construct();
			$this->set('controller', $this);
		}
		
		public function post($field = false) {
			// the only post that matters is the one for this attribute's name space
			$p = $_POST['akID'][$this->attributeKey->getAttributeKeyID()];
			if ($field) {
				return $p[$field];
			}
			return $p;
		}
		
		public function setupAndRun($method) {
			$args = func_get_args();
			$args = array_slice($args, 1);
			if ($method) {
				$this->task = $method;
			}
			if (method_exists($this, 'on_start')) {
				call_user_func_array(array($this, 'on_start'), array($method));
			}
			if ($method) {
				$this->runTask($method, $args);
			}
			
			if (method_exists($this, 'on_before_render')) {
				call_user_func_array(array($this, 'on_before_render'), array($method));
			}
		}
		
	}
	