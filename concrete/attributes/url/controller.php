<?php
namespace Concrete\Attribute\Url;

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

    public function getIconFormatter()
    {
        return new FontAwesomeIconFormatter('link');
    }

    public function type_form()
    {
        $this->set('form', \Core::make('helper/form'));
        $this->set('akIsRequired', $this->getAttributeKey() ? $this->getAttributeKey()->getAkIsRequired() : false);
    }

    public function validateForm($data)
    {
        $required = $this->getAttributeKey()->getAkIsRequired();
        $value = $data['value'];

        if (!$required) {
            return true;
        } elseif ($required && !filter_var($value, FILTER_VALIDATE_URL)) {
            return new Error(t('You must specify a valid url for %s', $this->getAttributeKey()
                ->getAttributeKeyDisplayName()),
                new AttributeField($this->getAttributeKey())
            );
        }

        return true;
    }
}
