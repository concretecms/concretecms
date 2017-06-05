<?php
namespace Concrete\Core\Error;

use Exception;
use JsonSerializable;

/**
 * Represents an error that can be safely shown to users.
 */
class UserException extends Exception implements JsonSerializable
{
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
