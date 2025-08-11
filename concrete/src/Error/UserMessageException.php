<?php

namespace Concrete\Core\Error;

use Concrete\Core\Error\ErrorList\Error\HtmlAwareErrorInterface;
use Exception;
use JsonSerializable;

/**
 * Represents an error that can be safely shown to users.
 */
class UserMessageException extends Exception implements JsonSerializable, HtmlAwareErrorInterface
{
    /**
     * Can this exception be added to the log?
     *
     * @var bool
     */
    protected $canBeLogged = false;

    /**
     * Does the message contain an HTML-formatted string?
     *
     * @since concrete5 8.5.0a3
     *
     * @var bool
     */
    private $messageContainsHtml = false;

    /**
     * Can this exception be added to the log?
     *
     * @return bool
     */
    public function canBeLogged()
    {
        return $this->canBeLogged;
    }

    /**
     * Can this exception be added to the log?
     *
     * @param bool $value
     *
     * @return $this
     */
    public function setCanBeLogged($value)
    {
        $this->canBeLogged = (bool) $value;

        return $this;
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
     * @see JsonSerializable::jsonSerialize()
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $result = [
            'error' => true,
            'errors' => [
                $this->getMessage(),
            ],
        ];
        if ($this->messageContainsHtml()) {
            $result['htmlErrorIndexes'] = [0];
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Exception::__toString()
     */
    public function __toString()
    {
        return $this->getMessage();
    }
}
