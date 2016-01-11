<?php
namespace Concrete\Attribute\Text;

use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Loader;
use Concrete\Core\Attribute\DefaultController;

class Controller extends DefaultController
{
    protected $searchIndexFieldDefinition = array(
        'type' => 'text',
        'options' => array('length' => 4294967295, 'default' => null, 'notnull' => false),
    );

    public function form()
    {
        if (is_object($this->attributeValue)) {
            $value = Loader::helper('text')->entities($this->getAttributeValue()->getValue());
        }
        echo Loader::helper('form')->text($this->field('value'), $value);
    }

    public function composer()
    {
        $value = null;
        if (is_object($this->attributeValue)) {
            $value = Loader::helper('text')->entities($this->getAttributeValue()->getValue());
        }
        echo Loader::helper('form')->text($this->field('value'), $value, array('class' => 'span5'));
    }

    public function getIconFormatter()
    {
        return new FontAwesomeIconFormatter('file-text');
    }
}
