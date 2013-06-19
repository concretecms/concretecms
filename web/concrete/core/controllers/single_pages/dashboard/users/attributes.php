<?php
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Dashboard_Users_Attributes extends Controller {
	
	public $helpers = array('form');
	
	public function __construct() {
		parent::__construct();
		$otypes = AttributeType::getList('user');
		$types = array();
		foreach($otypes as $at) {
			$types[$at->getAttributeTypeID()] = tc('AttributeTypeName', $at->getAttributeTypeName());
		}
		$this->set('types', $types);
	}
	
	public function delete($akID, $token = null){
		try {
			$ak = UserAttributeKey::getByID($akID); 
				
			if(!($ak instanceof UserAttributeKey)) {
				throw new Exception(t('Invalid attribute ID.'));
			}
	
			$valt = Loader::helper('validation/token');
			if (!$valt->validate('delete_attribute', $token)) {
				throw new Exception($valt->getErrorMessage());
			}
			
			$ak->delete();
			
			$this->redirect("/dashboard/users/attributes", 'attribute_deleted');
		} catch (Exception $e) {
			$this->set('error', $e);
		}
	}
	
	public function on_start() {
		$this->set('category', AttributeKeyCategory::getByHandle('user'));
	}
	
	public function activate($akID, $token = null) {
		try {
			$ak = UserAttributeKey::getByID($akID); 
				
			if(!($ak instanceof UserAttributeKey)) {
				throw new Exception(t('Invalid attribute ID.'));
			}
	
			$valt = Loader::helper('validation/token');
			if (!$valt->validate('attribute_activate', $token)) {
				throw new Exception($valt->getErrorMessage());
			}
			
			$ak->activate();
			
			$this->redirect("/dashboard/users/attributes", 'edit', $akID);
			
		} catch (Exception $e) {
			$this->set('error', $e);
		}
	}
	
	public function deactivate($akID, $token = null) {
			$ak = UserAttributeKey::getByID($akID); 
				
			if(!($ak instanceof UserAttributeKey)) {
				throw new Exception(t('Invalid attribute ID.'));
			}
	
			$valt = Loader::helper('validation/token');
			if (!$valt->validate('attribute_deactivate', $token)) {
				throw new Exception($valt->getErrorMessage());
			}
			
			$ak->deactivate();
			
			$this->redirect("/dashboard/users/attributes", 'edit', $akID);
	}
	
	public function select_type() {
		$atID = $this->request('atID');
		$at = AttributeType::getByID($atID);
		if(isset($at->atID) && $at->atID > 0) {
			$this->set('type', $at);
		} else {
			throw new Exception(t('Invalid Attribute Type.'));
		}
	}
	
	public function view() {
		$attribs = UserAttributeKey::getList();
		$this->set('attribs', $attribs);
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
			$ak = UserAttributeKey::add($type, $this->post());
			$this->redirect('/dashboard/users/attributes/', 'attribute_created');
		}
	}

	public function attribute_deleted() {
		$this->set('message', t('User Attribute Deleted.'));
	}
	
	public function attribute_created() {
		$this->set('message', t('User Attribute Created.'));
	}

	public function attribute_updated() {
		$this->set('message', t('User Attribute Updated.'));
	}
	
	public function edit($akID = 0) {
		if ($this->post('akID')) {
			$akID = $this->post('akID');
		}
		$key = UserAttributeKey::getByID($akID);
		if (!is_object($key) || $key->isAttributeKeyInternal()) {
			$this->redirect('/dashboard/users/attributes');
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
				$key->update($this->post());
				$this->redirect('/dashboard/users/attributes', 'attribute_updated');
			}
		}
	}
	
}