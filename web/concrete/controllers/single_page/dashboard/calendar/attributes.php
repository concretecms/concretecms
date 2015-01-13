<?php
namespace Concrete\Controller\SinglePage\Dashboard\Calendar;
use \Concrete\Core\Page\Controller\DashboardPageController;
use \Concrete\Core\Attribute\Type as AttributeType;
use \Concrete\Core\Attribute\Key\Category as AttributeKeyCategory;
use Concrete\Core\Attribute\Key\EventKey;
use Exception;

class Attributes extends DashboardPageController {
	
	public $helpers = array('form');

	public function delete($akID, $token = null){
		try {
			$ak = EventKey::getByID($akID);
				
			if(!($ak instanceof EventKey)) {
				throw new Exception(t('Invalid attribute ID.'));
			}
	
			$valt = \Loader::helper('validation/token');
			if (!$valt->validate('delete_attribute', $token)) {
				throw new Exception($valt->getErrorMessage());
			}
			
			$ak->delete();
			
			$this->redirect("/dashboard/calendar/attributes", 'attribute_deleted');
		} catch (Exception $e) {
			$this->set('error', $e);
		}
	}
	
	public function select_type() {
		$atID = $this->request('atID');
		$at = AttributeType::getByID($atID);
		$this->set('type', $at);
	}
	
	public function view() {
		$attribs = EventKey::getList();
		$this->set('attribs', $attribs);
	}
	
	public function on_start() {
		parent::on_start();
		$this->set('category', AttributeKeyCategory::getByHandle('event'));
		$otypes = AttributeType::getList('event');
		$types = array();
		foreach($otypes as $at) {
			$types[$at->getAttributeTypeID()] = $at->getAttributeTypeDisplayName();
		}
		$this->set('types', $types);
	}
	
	public function add() {
		$this->select_type();
		$type = $this->get('type');
		$cnt = $type->getController();
		$this->error = $cnt->validateKey($this->post());
		if (!$this->error->has()) {
			$type = AttributeType::getByID($this->post('atID'));
			$ak = EventKey::add($type, $this->post());
			$this->redirect('/dashboard/calendar/attributes/', 'attribute_created');
		}
	}

	public function attribute_deleted() {
		$this->set('message', t('Attribute Deleted.'));
	}
	
	public function attribute_created() {
		$this->set('message', t('Attribute Created.'));
	}

	public function attribute_updated() {
		$this->set('message', t('Attribute Updated.'));
	}
	
	public function edit($akID = 0) {
		if ($this->post('akID')) {
			$akID = $this->post('akID');
		}
		$key = EventKey::getByID($akID);
		if (!is_object($key) || $key->isAttributeKeyInternal()) {
			$this->redirect('/dashboard/calendar/attributes');
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
				$this->redirect('/dashboard/calendar/attributes', 'attribute_updated');
			}
		}
	}
	
}