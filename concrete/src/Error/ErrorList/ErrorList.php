<?php

namespace Concrete\Core\Error\ErrorList;

use ArrayAccess;
use Concrete\Core\Error\ErrorList\Error\Error;
use Concrete\Core\Error\ErrorList\Error\ErrorInterface;
use Concrete\Core\Error\ErrorList\Error\ExceptionError;
use Concrete\Core\Error\ErrorList\Error\ThrowableError;
use Concrete\Core\Error\ErrorList\Field\Field;
use Concrete\Core\Error\ErrorList\Field\FieldInterface;
use Concrete\Core\Error\ErrorList\Formatter\JsonFormatter;
use Concrete\Core\Error\ErrorList\Formatter\StandardFormatter;
use Concrete\Core\Error\ErrorList\Formatter\TextFormatter;
use Exception;
use JsonSerializable;
use Symfony\Component\HttpFoundation\JsonResponse;
use Throwable;

class ErrorList implements ArrayAccess, JsonSerializable
{
    /**
     * @var \Concrete\Core\Error\ErrorList\Error\ErrorInterface[]
     */
    protected $errors = [];

    /**
     * @return string
     */
    public function __toString()
    {
        $formatter = new StandardFormatter($this);

        return (string) $formatter->render();
    }

    /**
     * {@inheritdoc}
     *
     * @see \ArrayAccess::offsetExists()
     */
    public function offsetExists($offset)
    {
        return $this->errors[$offset];
    }

    /**
     * {@inheritdoc}
     *
     * @see \ArrayAccess::offsetGet()
     *
     * @return \Concrete\Core\Error\ErrorList\Error\ErrorInterface|null
     */
    public function offsetGet($offset)
    {
        return array_get($this->errors, $offset);
    }

    /**
     * {@inheritdoc}
     *
     * @see \ArrayAccess::offsetSet()
     */
    public function offsetSet($offset, $value)
    {
        if ($offset === null) {
            $this->add($value);
        } else {
            $this->errors[$offset] = $value;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \ArrayAccess::offsetUnset()
     */
    public function offsetUnset($offset)
    {
        unset($this->errors[$offset]);
    }

    /**
     * Add an error message/object or exception to the internal error array.
     *
     * @param \Concrete\Core\Error\ErrorList\Error\ErrorInterface|\Exception|\Throwable|self|string $e the error(s) to be added
     * @param bool $isHtml set to true if error messages are in HTML format if not otherwise specified
     * @param string|null $fieldName
     * @param string|null $fieldDisplayName
     *
     * @return $this
     *
     * @since concrete5 8.5.0a3
     */
    public function addError($e, $isHtml = false, $fieldName = null, $fieldDisplayName = null)
    {
        if ($e instanceof self) {
            foreach ($e->getList() as $error) {
                $this->addError($error, $isHtml, $fieldName, $fieldDisplayName);
            }
        } elseif ($e instanceof ErrorInterface) {
            $this->errors[] = $e;
        } else {
            if ($e instanceof Exception) {
                $error = new ExceptionError($e);
            } elseif ($e instanceof Throwable) {
                $error = new ThrowableError($e);
            } else {
                $error = new Error($e);
            }
            $error->setMessageContainsHtml($isHtml);
            if ($fieldName) {
                $field = new Field($fieldName);
                if ($fieldDisplayName) {
                    $field->setDisplayName($fieldDisplayName);
                }
                $error->setField($field);
            }
            $this->add($error);
        }

        return $this;
    }

    /**
     * Add an error message/object or exception to the internal error array (error messages are in plain text if not otherwise specified).
     *
     * @param \Concrete\Core\Error\ErrorList\Error\ErrorInterface|\Exception|\Throwable|self|string $e the error(s) to be added
     * @param string|null $fieldName
     * @param string|null $fieldDisplayName
     *
     * @return $this
     */
    public function add($e, $fieldName = null, $fieldDisplayName = null)
    {
        return $this->addError($e, false, $fieldName, $fieldDisplayName);
    }

    /**
     * Add an error message/object or exception to the internal error array (error messages are in HTML if not otherwise specified).
     *
     * @param \Concrete\Core\Error\ErrorList\Error\ErrorInterface|\Exception|\Throwable|self|string $e the error(s) to be added
     * @param string|null $fieldName
     * @param string|null $fieldDisplayName
     *
     * @return $this
     *
     * @since concrete5 8.5.0a3
     */
    public function addHtml($e, $fieldName = null, $fieldDisplayName = null)
    {
        return $this->addError($e, true, $fieldName, $fieldDisplayName);
    }

    /**
     * Get the list of errors contained in this error list.
     *
     * @return \Concrete\Core\Error\ErrorList\Error\ErrorInterface[]
     */
    public function getList()
    {
        return $this->errors;
    }

    /**
     * Returns whether or not this error list has more than zero error registered within it.
     *
     * @return bool
     */
    public function has()
    {
        return !empty($this->errors);
    }

    /**
     * @deprecated Use the StandardFormatter class
     *
     * @return string
     *
     * @see \Concrete\Core\Error\ErrorList\Formatter\StandardFormatter
     */
    public function output()
    {
        $formatter = new StandardFormatter($this);
        echo $formatter->render();
    }

    /**
     * @deprecated Use the JsonFormatter class
     *
     * @return string
     *
     * @see \Concrete\Core\Error\ErrorList\Formatter\JsonFormatter
     */
    public function outputJSON()
    {
        $formatter = new JsonFormatter($this);
        echo $formatter->render();
    }

    /**
     * {@inheritdoc}
     *
     * @see \JsonSerializable::jsonSerialize()
     *
     * @return array|null
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $formatter = new JsonFormatter($this);

        return $formatter->asArray();
    }

    /**
     * Render this error list as a plain text.
     *
     * @return string
     */
    public function toText()
    {
        $formatter = new TextFormatter($this);

        return $formatter->getText();
    }

    /**
     * Does this list contain error associated to a field?
     *
     * @param \Concrete\Core\Error\ErrorList\Field\FieldInterface|string $field
     *
     * @return bool
     */
    public function containsField($field)
    {
        $identifier = $field instanceof FieldInterface ? $field->getFieldElementName() : $field;
        foreach ($this->getList() as $error) {
            $field = $error->getField();
            if (is_object($field) && $field->getFieldElementName() == $identifier) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the error message (if any) associated to a field.
     *
     * @param \Concrete\Core\Error\ErrorList\Field\FieldInterface|string $field
     *
     * @return string|false false if no error is associated to the field, a string otherwise
     */
    public function getMessage($field)
    {
        $identifier = ($field instanceof FieldInterface) ? $field->getFieldElementName() : $field;
        foreach ($this->getList() as $error) {
            $field = $error->getField();
            if (is_object($field) && $field->getFieldElementName() == $identifier) {
                return $error->getMessage();
            }
        }

        return false;
    }

    /**
     * Create a JSON response describing the errors in this list.
     *
     * @param int $errorCode The HTTP response code to be sent to the client
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function createResponse($errorCode = JsonResponse::HTTP_BAD_REQUEST)
    {
        return new JsonResponse($this->jsonSerialize(), $errorCode);
    }
}
