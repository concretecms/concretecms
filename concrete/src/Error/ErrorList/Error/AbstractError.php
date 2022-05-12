<?php

namespace Concrete\Core\Error\ErrorList\Error;

use Concrete\Core\Error\ErrorList\Field\FieldInterface;

abstract class AbstractError implements HtmlAwareErrorInterface
{
    /**
     * The field associated to the error.
     *
     * @var \Concrete\Core\Error\ErrorList\Field\FieldInterface|null
     */
    protected $field;

    /**
     * Does the message contain an HTML-formatted string?
     *
     * @since concrete5 8.5.0a3
     *
     * @var bool
     */
    private $messageContainsHtml = false;

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
     * @see \Concrete\Core\Error\ErrorList\Error\HtmlAwareErrorInterface::messageContainsHtml()
     * @since concrete5 8.5.0a3
     */
    public function messageContainsHtml()
    {
        return $this->messageContainsHtml;
    }

    /**
     * Does the message contain an HTML-formatted string?
     *
     * @param bool $value
     *
     * @return $this
     *
     * @since concrete5 8.5.0a3
     */
    public function setMessageContainsHtml($value)
    {
        $this->messageContainsHtml = (bool) $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \JsonSerializable::jsonSerialize()
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $r = [
            'message' => $this->getMessage(),
            'messageContainsHtml' => $this->messageContainsHtml(),
        ];
        if ($this->field) {
            $r['field'] = $this->field;
        }

        return $r;
    }
}
