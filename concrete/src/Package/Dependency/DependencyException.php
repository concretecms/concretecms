<?php
namespace Concrete\Core\Package\Dependency;

use Concrete\Core\Error\ErrorList\Error\ErrorInterface;
use LogicException;

/**
 * Package dependency failure.
 */
abstract class DependencyException extends LogicException implements ErrorInterface
{
    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->getMessage();
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return ['message' => $this->getMessage()];
    }
}
