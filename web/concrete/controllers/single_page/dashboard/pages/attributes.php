<?php
namespace Concrete\Controller\SinglePage\Dashboard\Pages;
use \Concrete\Core\Page\Controller\DashboardPageController;
use \Concrete\Core\Attribute\Type as AttributeType;
use \Concrete\Core\Attribute\Key\Category as AttributeKeyCategory;
use CollectionAttributeKey;
use Loader;
use Exception;
class Attributes extends DashboardPageController {
	
	public $helpers = array('form');

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
		parent::on_start();
		$this->set('category', AttributeKeyCategory::getByHandle('collection'));
		$otypes = AttributeType::getList('collection');
		$types = array();
		foreach($otypes as $at) {
			$types[$at->getAttributeTypeID()] = $at->getAttributeTypeDisplayName();
		}
		$this->set('types', $types);
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
			$this->error = $e;
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
			$this->error = $e;
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
				$this->error = $e;
			} else {
				$type = AttributeType::getByID($this->post('atID'));
				$key->update($this->post());
				$this->redirect('/dashboard/pages/attributes/', 'attribute_updated');
			}
		}
	}
	
}