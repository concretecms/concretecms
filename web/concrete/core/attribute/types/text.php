<?
defined('C5_EXECUTE') or die("Access Denied.");

Loader::model('attribute/types/default/controller');

class Concrete5_Controller_AttributeType_Text extends DefaultAttributeTypeController  {

	protected $searchIndexFieldDefinition = 'X NULL';

	public function form() {
		if (is_object($this->attributeValue)) {
			$value = Loader::helper('text')->entities($this->getAttributeValue()->getValue());
		}
		print Loader::helper('form')->text($this->field('value'), $value);
	}
	
	public function composer() {
		if (is_object($this->attributeValue)) {
			$value = Loader::helper('text')->entities($this->getAttributeValue()->getValue());
		}
		print Loader::helper('form')->text($this->field('value'), $value, array('class' => 'span5'));
	}


}