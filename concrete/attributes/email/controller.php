<?php
namespace Concrete\Attribute\Email;

use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Attribute\DefaultController;
use Concrete\Core\Error\ErrorList\Error\Error;
use Concrete\Core\Error\ErrorList\Error\FieldNotPresentError;
use Concrete\Core\Error\ErrorList\Field\AttributeField;

class Controller extends DefaultController
{

    public $helpers = array('form');

    public function form()
    {
        $value = null;
        if (is_object($this->attributeValue)) {
            $value = $this->app->make('helper/text')->entities($this->getAttributeValue()->getValue());
        }
        $this->set('value', $value);
    }

    public function getIconFormatter()
    {
        return new FontAwesomeIconFormatter('envelope');
    }

    public function validateForm($data)
    {
        if (!$data['value']) {
            return new FieldNotPresentError(new AttributeField($this->getAttributeKey()));
        } else {
            $fh = \Core::make('helper/validation/strings');
            if (!$fh->email($data['value'])) {
                return new Error(t('Invalid email address.'), new AttributeField($this->getAttributeKey()));
            } else {
                return true;
            }
        }
    }
}
