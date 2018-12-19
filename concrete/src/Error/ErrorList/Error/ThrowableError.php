<?php

namespace Concrete\Core\Error\ErrorList\Error;

use Concrete\Core\Error\ErrorList\Field\FieldInterface;
use Throwable;

class ThrowableError extends AbstractError
{
    /**
     * The associated Throwable.
     *
     * @var \Throwable
     */
    protected $throwable;

    /**
     * Class constructor.
     *
     * @param \Throwable $throwable
     * @param \Concrete\Core\Error\ErrorList\Field\FieldInterface|null $field
     */
    public function __construct(Throwable $throwable, FieldInterface $field = null)
    {
        $this->throwable = $throwable;
        if ($field) {
            $this->setField($field);
        }
    }

    /**
     * Get the associated Throwable.
     *
     * @return \Throwable
     */
    public function getThrowable()
    {
        return $this->throwable;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Error\ErrorList\Error\ErrorInterface::getMessage()
     */
    public function getMessage()
    {
        return $this->getThrowable()->getMessage();
    }
}
