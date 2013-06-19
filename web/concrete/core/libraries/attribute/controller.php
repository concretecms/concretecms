<?
defined('C5_EXECUTE') or die("Access Denied.");

	class Concrete5_Library_AttributeTypeController extends Controller {
		
		protected $identifier;
		protected static $sets = array();
	 	protected $attributeKey;
		protected $requestArray = false;
		
		public function setRequestArray($array) {
			$this->requestArray = $array;
		}
		
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
	 	
	 	public function exportKey($ak) {
	 		return $ak;
	 	}
	 	
	 	public function importValue(SimpleXMLElement $akv) {
			if (isset($akv->value)) {
				return (string) $akv->value;
			}
	 	}
	 	
	 	public function exportValue(SimpleXMLElement $akv) {
			$val = $this->attributeValue->getValue();
			if (is_object($val)) {
				$val = (string) $val;
			}

			$cnode = $akv->addChild('value');
			$node = dom_import_simplexml($cnode);
			$no = $node->ownerDocument;
			$node->appendChild($no->createCDataSection($val));
	 		return $cnode;
	 	}
	 	
	 	public function importKey($akn) {
	 		
	 	}
	 	
	 	
	 	protected function getAttributeValueID() {
	 		if (is_object($this->attributeValue)) {
		 		return $this->attributeValue->getAttributeValueID();
		 	}
	 	}
	 	
		public function field($fieldName) {
			return 'akID[' . $this->attributeKey->getAttributeKeyID() . '][' . $fieldName . ']';
		}

		public function label($customText = false) {
			if ($customText == false) {
				$text = tc('AttributeKeyName', $this->attributeKey->getAttributeKeyName());
			} else {
				$text = $customText;
			}
			print Loader::helper('form')->label($this->field('value'), $text);
		}
		
		public function __construct($attributeType) {
			$this->identifier = $attributeType->getAttributeTypeID();
			$this->attributeType = $attributeType;
			parent::__construct();
			$this->set('controller', $this);
		}
		
		public function post($field = false) {
			// the only post that matters is the one for this attribute's name space
			$req = ($this->requestArray == false) ? $_POST : $this->requestArray;
			if (is_object($this->attributeKey) && is_array($req['akID'])) {
				$p = $req['akID'][$this->attributeKey->getAttributeKeyID()];
				if ($field) {
					return $p[$field];
				}
				return $p;
			}			
			return parent::post($field);
		}

		public function request($field = false) {
			$req = ($this->requestArray == false) ? $_REQUEST : $this->requestArray;
			
			if (is_object($this->attributeKey) && is_array($req['akID'])) {
				$p = $req['akID'][$this->attributeKey->getAttributeKeyID()];
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
				$this->on_start($method);
			}
			if ($method == 'composer') {
				$method = array('composer', 'form');
			}
			
			if ($method) {
				$this->runTask($method, $args);
			}
			
			if (method_exists($this, 'on_before_render')) {
				$this->on_before_render($method);
			}
		}

		public function saveKey() {
		
		}
		
		public function duplicateKey() {
		
		}
		
		// return a string we can use to search by
		public function searchKeywords($keywords, $list = false) {
			$db = Loader::db();
			$qkeywords = $db->quote('%' . $keywords . '%');
			return 'ak_' . $this->attributeKey->getAttributeKeyHandle() . ' like '.$qkeywords.' ';
		}
		
		/* Automatically run when an attribute key is added or updated
		* @return ValidationError
		*/		
		public function validateKey($args = false) {
			if ($args == false) {
				$args =  $this->post();
			}
			$val = Loader::helper('validation/form');
			$valt = Loader::helper('validation/token');
			$val->setData($args);
			$val->addRequired("akHandle", t("Handle required."));
			$val->addRequired("akName", t('Name required.'));
			$val->addRequired("atID", t('Type required.'));
			$val->test();
			$error = $val->getError();
		
			if (!$valt->validate('add_or_update_attribute')) {
				$error->add($valt->getErrorMessage());
			}
			
			if(preg_match("/[^A-Za-z0-9\_]/", $args['akHandle'])) {
				$error->add(t('Attribute handles may only contain letters, numbers and underscore "_" characters'));
			}
			
			$akc = AttributeKeyCategory::getByID($args['akCategoryID']);
			if (is_object($akc)) {
				if ($akc->handleExists($args['akHandle'])) {
					if (is_object($this->attributeKey)) {
						$ak2 = $akc->getAttributeKeyByHandle($args['akHandle']);
						if ($ak2->getAttributeKeyID() != $this->attributeKey->getAttributeKeyID()) {
							$error->add(t("An attribute with the handle %s already exists.", $akHandle));
						}
					} else {
						$error->add(t("An attribute with the handle %s already exists.", $akHandle));
					}
				}
			} else {
				$error->add('Invalid attribute category.');
			}
			
			return $error;			
		}
		
	}
	