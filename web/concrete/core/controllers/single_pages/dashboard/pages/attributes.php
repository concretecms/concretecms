<?php
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_Pages_Attributes extends Controller {
	
	public $helpers = array('form');
	
	public function __construct() {
		parent::__construct();
		$otypes = AttributeType::getList('collection');
		$types = array();
		foreach($otypes as $at) {
			$types[$at->getAttributeTypeID()] = tc('AttributeTypeName', $at->getAttributeTypeName());
		}
		$this->set('types', $types);
	}

	public function attribute_updated() {
		$this->set('message', t('Page Attribute Updated.'));
	}
	
	public function attribute_created() {
		$this->set('message', t('Page Attribute Created.'));
	}
	
	public function attribute_deleted() {
		$this->set('message', t('Page Attribute Deleted.'));
	}

	public function on_start() {
		$this->set('disableThirdLevelNav', true);
		$this->set('category', AttributeKeyCategory::getByHandle('collection'));
	}
	
	public function delete($akID, $token = null){
		try {
			$ak = CollectionAttributeKey::getByID($akID); 
				
			if(!($ak instanceof CollectionAttributeKey)) {
				throw new Exception(t('Invalid attribute ID.'));
			}
	
			$valt = Loader::helper('validation/token');
			if (!$valt->validate('delete_attribute', $token)) {
				throw new Exception($valt->getErrorMessage());
			}
			
			$ak->delete();
			
			$this->redirect("/dashboard/pages/attributes", 'attribute_deleted');
		} catch (Exception $e) {
			$this->set('error', $e);
		}
	}
	
	public function select_type() {
		$atID = $this->request('atID');
		$at = AttributeType::getByID($atID);
		$this->set('type', $at);
	}
	
	public function add() {
		$this->select_type();
		$type = $this->get('type');
		$cnt = $type->getController();
		$e = $cnt->validateKey($this->post());
		if ($e->has()) {
			$this->set('error', $e);
		} else {
			$type = AttributeType::getByID($this->post('atID'));
			$ak = CollectionAttributeKey::add($type, $this->post());
			$this->redirect('/dashboard/pages/attributes/', 'attribute_created');
		}
	}
	
	public function edit($akID = 0) {
		if ($this->post('akID')) {
			$akID = $this->post('akID');
		}
		$key = CollectionAttributeKey::getByID($akID);
		if (!is_object($key) || $key->isAttributeKeyInternal()) {
			$this->redirect('/dashboard/pages/attributes');
		}
		$type = $key->getAttributeType();
		$this->set('key', $key);
		$this->set('type', $type);
		
		if ($this->isPost()) {
			$cnt = $type->getController();
			$cnt->setAttributeKey($key);
			$e = $cnt->validateKey($this->post());
			if ($e->has()) {
				$this->set('error', $e);
			} else {
				$type = AttributeType::getByID($this->post('atID'));
				$key->update($this->post());
				$this->redirect('/dashboard/pages/attributes/', 'attribute_updated');
			}
		}
	}
	
}