<?php defined('C5_EXECUTE') or die('Access Denied');

class Concrete5_Controller_Dashboard_System_Attributes_Types extends DashboardBaseController {
	
	public function add_attribute_type() {
		$pat = PendingAttributeType::getByHandle($this->post('atHandle'));
		if (is_object($pat)) {
			$pat->install();
		}
		$this->redirect('dashboard/system/attributes/types', 'saved', 'attribute_type_added');
	}

	public function save_attribute_type_associations() {
		$list = AttributeKeyCategory::getList();
		foreach($list as $cat) {
			$cat->clearAttributeKeyCategoryTypes();
			if (is_array($this->post($cat->getAttributeKeyCategoryHandle()))) {
				foreach($this->post($cat->getAttributeKeyCategoryHandle()) as $id) {
					$type = AttributeType::getByID($id);
					$cat->associateAttributeKeyType($type);
				}
			}
		}

		$this->redirect('dashboard/system/attributes/types', 'saved', 'associations_updated');
	}

	public function saved($mode = false) {

		if ($mode != false) {
			switch($mode) {
				case 'associations_updated':
					$this->set('message', 'Attribute Type Associations saved.');
					break;
				case 'attribute_type_added':
					$this->set('message', 'Attribute Type added.');
					break;
			}
		}
	}
	
}