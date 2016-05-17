<?php
namespace Concrete\Core\Error\ErrorBag\Error;

use Concrete\Core\Error\ErrorBag\Field\FieldInterface;

class FieldNotPresentError extends AbstractError
{

    /**
     * Error constructor.
     * @param $message
     */
    public function __construct(FieldInterface $field)
    {
        $this->setField($field);
    }

    public function getMessage()
    {
        return t('The field %s is required.', $this->field->getDisplayName());
    }


}
