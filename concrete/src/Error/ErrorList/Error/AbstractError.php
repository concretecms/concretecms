<?php

namespace Concrete\Core\Error\ErrorList\Error;

use Concrete\Core\Error\ErrorList\Field\FieldInterface;

abstract class AbstractError implements ErrorInterface
{
    /**
     * The field associated to the error.
     *
     * @var \Concrete\Core\Error\ErrorList\Field\FieldInterface
     */
    protected $field;

    /**
     * Get the error message.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getMessage();
    }

    /**
     * Get the field associated to the error.
     *
     * @return \Concrete\Core\Error\ErrorList\Field\FieldInterface|null
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Set the field associated to the error.
     *
     * @param \Concrete\Core\Error\ErrorList\Field\FieldInterface $field
     */
    public function setField(FieldInterface $field)
    {
        $this->field = $field;
    }

    /**
     * {@inheritdoc}
     *
     * @see \JsonSerializable::jsonSerialize()
     */
    public function jsonSerialize()
    {
        $r = [
            'message' => $this->getMessage(),
        ];
        if ($this->field) {
            $r['field'] = $this->field;
        }

        return $r;
    }
}
