<?php
namespace Concrete\Attribute\Telephone;

use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Attribute\DefaultController;
use Concrete\Core\Error\ErrorList\Error\Error;
use Concrete\Core\Error\ErrorList\Field\AttributeField;

class Controller extends DefaultController
{
    public $helpers = ['form'];

    public function form()
    {
        $value = null;
        if (is_object($this->attributeValue)) {
            $value = $this->app->make('helper/text')->entities($this->getAttributeValue()->getValue());
        }
        $this->set('value', $value);
    }

    public function composer()
    {
        $this->form();
    }

    public function getIconFormatter()
    {
        return new FontAwesomeIconFormatter('phone');
    }

    public function type_form()
    {
        $this->set('form', \Core::make('helper/form'));
        $this->set('akIsRequired', $this->getAttributeKey() ? $this->getAttributeKey()->getAkIsRequired() : false);
    }

    public function validateForm($data)
    {
        $required = $this->getAttributeKey()->getAkIsRequired();

        if (!$required) {
            return true;
        } elseif ($required && !$data['value']->getValue()) {
            return new Error(t('You must specify a valid phone number for %s', $this->getAttributeKey()
                ->getAttributeKeyDisplayName()),
                new AttributeField($this->getAttributeKey())
            );
        }

        return true;
    }
}
