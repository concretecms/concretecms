<?php
namespace Concrete\Core\Error\ErrorList\Error;

use Concrete\Core\Error\ErrorList\Field\FieldInterface;

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
