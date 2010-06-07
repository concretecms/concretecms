<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::model('attribute/types/default/controller');

class TextAttributeTypeController extends DefaultAttributeTypeController  {

	protected $searchIndexFieldDefinition = 'X NULL';

	public function form() {
		if (is_object($this->attributeValue)) {
			$value = $this->getAttributeValue()->getValue();
		}
		print Loader::helper('form')->text($this->field('value'), $value);
	}

}