<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('collection_attributes');
class DashboardPagesTypesAttributesController extends Controller {
	
	public $helpers = array('form');
	
	public function __construct() {
		parent::__construct();
		$otypes = AttributeType::getList();
		$types = array();
		foreach($otypes as $at) {
			$types[$at->getAttributeTypeID()] = $at->getAttributeTypeName();
		}
		$this->set('types', $types);
	}
	
	public function select_type() {
		$atID = $this->request('atID');
		$at = AttributeType::getByID($atID);
		$this->set('type', $at);
		$this->set('category', AttributeKeyCategory::getByHandle('collection'));
	}
	
	public function add() {
		$this->select_type();
		$type = $this->get('type');
		$cnt = $type->getController();
		$ak = $cnt->addKey();
	}
	
	public function attribute_type_passthru($atID) {
		
	}
	
}