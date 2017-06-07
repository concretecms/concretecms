<?php
namespace Concrete\Core\Error;

use Exception;
use JsonSerializable;

/**
 * Represents an error that can be safely shown to users.
 */
class UserMessageException extends Exception implements JsonSerializable
{
    /**
     * Can this exception be added to the log?
     *
     * @var bool
     */
    protected $canBeLogged = false;

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
     * @see JsonSerializable::jsonSerialize()
     */
    public function jsonSerialize()
    {
        return [
            'error' => true,
            'errors' => [
                $this->getMessage(),
            ],
        ];
    }
}
