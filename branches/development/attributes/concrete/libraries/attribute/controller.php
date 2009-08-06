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
	 	
	 	public function getAttributeType() {
	 		return $this->attributeType;
	 	}
	 	
	 	protected function getAttributeValueID() {
	 		if (is_object($this->attributeValue)) {
		 		return $this->attributeValue->getAttributeValueID();
		 	}
	 	}
	 	
		public function field($fieldName) {
			return 'akID[' . $this->attributeKey->getAttributeKeyID() . '][' . $fieldName . ']';
		}


		public function __construct($attributeType) {
			$this->identifier = $attributeType->getAttributeTypeID();
			$this->attributeType = $attributeType;
			parent::__construct();
			$this->set('controller', $this);
		}
		
		public function post($field = false) {
			// the only post that matters is the one for this attribute's name space
			if (is_object($this->attributeKey) && is_array($_POST['akID'])) {
				$p = $_POST['akID'][$this->attributeKey->getAttributeKeyID()];
				if ($field) {
					return $p[$field];
				}
				return $p;
			}
			
			return parent::post($field);
		}

		public function request($field = false) {
			if (is_object($this->attributeKey) && is_array($_REQUEST['akID'])) {
				$p = $_REQUEST['akID'][$this->attributeKey->getAttributeKeyID()];
				if ($field) {
					return $p[$field];
				}
				return $p;
			}
			
			return parent::request($field);
		}
		
		public function getView() {
			$av = new AttributeTypeView($this->attributeType, $this->attributeKey, $this->attributeValue);
			return $av;
		}
		
		public function getSearchIndexFieldDefinition() {
			return $this->searchIndexFieldDefinition;
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

		public function saveKey() {
		
		}
		
		/* Automatically run when an attribute key is added or updated
		* @return ValidationError
		*/		
		public function validateKey() {
			$val = Loader::helper('validation/form');
			$valt = Loader::helper('validation/token');
			$val->setData($this->post());
			$val->addRequired("akHandle", t("Handle required."));
			$val->addRequired("akName", t('Name required.'));
			$val->addRequired("atID", t('Type required.'));
			$val->test();
			$error = $val->getError();
		
			if (!$valt->validate('add_or_update_attribute')) {
				$error->add($valt->getErrorMessage());
			}
			
			$akc = AttributeKeyCategory::getByID($this->post('akCategoryID'));
			if ($akc->handleExists($this->post('akHandle'))) {
				if ((!is_object($this->attributeKey)) || ($this->attributeKey->getAttributeKeyHandle() != $this->post('akHandle'))) {
					$error->add(t("An attribute with the handle %s already exists.", $akHandle));
				}
			}		
			
			return $error;			
		}
		
	}
	