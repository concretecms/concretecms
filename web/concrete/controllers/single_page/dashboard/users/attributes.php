<?php
namespace Concrete\Controller\SinglePage\Dashboard\Users;
use \Concrete\Core\Page\Controller\DashboardPageController;
use UserAttributeKey;
use Loader;
use Exception;
use \Concrete\Core\Attribute\Key\Category as AttributeKeyCategory;
use \Concrete\Core\Attribute\Type as AttributeType;

class Attributes extends DashboardPageController {
	
	public $helpers = array('form');

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
        parent::on_start();
        $this->set('category', AttributeKeyCategory::getByHandle('user'));
		$otypes = AttributeType::getAttributeTypeList('user');
		$types = array();
		foreach($otypes as $at) {
			$types[$at->getAttributeTypeID()] = $at->getAttributeTypeDisplayName();
		}
		$this->set('types', $types);

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
		if(is_object($at) && $at->getAttributeTypeID() > 0) {
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
			$this->error = $e;
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
				$this->error = $e;
			} else {
				$key->update($this->post());
				$this->redirect('/dashboard/users/attributes', 'attribute_updated');
			}
		}
	}
	
}