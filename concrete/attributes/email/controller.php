<?php

namespace Concrete\Attribute\Email;

use Concrete\Core\Attribute\DefaultController;
use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Error\ErrorList\Error\Error;
use Concrete\Core\Error\ErrorList\Error\FieldNotPresentError;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Error\ErrorList\Field\AttributeField;
use Concrete\Core\Validator\String\EmailValidator;

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
        return new FontAwesomeIconFormatter('envelope');
    }

    public function validateForm($data)
    {
        if (!$data['value']) {
            return new FieldNotPresentError(new AttributeField($this->getAttributeKey()));
        } else {
            $e = $this->app->make('error');
            if (!$this->app->make(EmailValidator::class)->isValid($data['value'], $e)) {
                return new Error($e->toText(), new AttributeField($this->getAttributeKey()));
            } else {
                return true;
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\SimpleTextExportableAttributeInterface::updateAttributeValueFromTextRepresentation()
     */
    public function updateAttributeValueFromTextRepresentation($textRepresentation, ErrorList $warnings)
    {
        $good = true;
        if ($textRepresentation !== '') {
            if (!$this->app->make(EmailValidator::class)->isValid($textRepresentation)) {
                $good = false;
                $warnings->add(t('"%1$s" is not a valid email address for the attribute with handle %2$s', $textRepresentation, $this->attributeKey->getAttributeKeyHandle()));
            }
        }
        if ($good) {
            $value = parent::updateAttributeValueFromTextRepresentation($textRepresentation, $warnings);
        } else {
            $value = $this->getAttributeValueObject();
        }

        return $value;
    }
}
