<?php

namespace Concrete\Core\Error\ErrorList\Error;

use Concrete\Core\Error\ErrorList\Field\FieldInterface;

class FieldNotPresentError extends AbstractError
{
    /**
     * Class constructor.
     *
     * @param \Concrete\Core\Error\ErrorList\Field\FieldInterface $field
     */
    public function __construct(FieldInterface $field)
    {
        $this->setField($field);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Error\ErrorList\Error\ErrorInterface::getMessage()
     */
    public function getMessage()
    {
        return t('The field %s is required.', $this->getField()->getDisplayName());
    }
}
