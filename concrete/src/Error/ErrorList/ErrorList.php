<?php
namespace Concrete\Core\Error\ErrorList;

use Concrete\Core\Error\ErrorList\Error\Error;
use Concrete\Core\Error\ErrorList\Error\ErrorInterface;
use Concrete\Core\Error\ErrorList\Error\ExceptionError;
use Concrete\Core\Error\ErrorList\Field\Field;
use Concrete\Core\Error\ErrorList\Field\FieldInterface;
use Concrete\Core\Error\ErrorList\Formatter\JsonFormatter;
use Concrete\Core\Error\ErrorList\Formatter\StandardFormatter;

class ErrorList implements \ArrayAccess, \JsonSerializable
{

    protected $errors = array();

    /**
     * Whether a offset exists.
     *
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     *
     * @return bool true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     *
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return $this->errors[$offset];
    }

    /**
     * Offset to retrieve.
     *
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     *
     * @return mixed Can return all value types.
     *
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return array_get($this->errors, $offset);
    }

    /**
     * Offset to set.
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     *
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->add($value);
        } else {
            $this->errors[$offset] = $value;
        }
    }

    /**
     * Offset to unset.
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     *
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        unset($this->errors[$offset]);
    }

    /**
     * Adds an error object or exception to the internal error array.
     *
     * @param \Exception | string $e
     */
    public function add($e, $fieldName = null, $fieldDisplayName = null)
    {
        if ($e instanceof self) {
            foreach ($e->getList() as $error) {
                $this->add($error);
            }
        } else if ($e instanceof ErrorInterface) {
            $this->errors[] = $e;
        } else {
            if (is_object($e) && ($e instanceof \Exception)) {
                $error = new ExceptionError($e);
            } else {
                $error = new Error($e);
            }
            if ($fieldName) {
                $field = new Field($fieldName);
                if ($fieldDisplayName) {
                    $field->setDisplayName($fieldDisplayName);
                }
                $error->setField($field);
            }
            $this->add($error);
        }
    }

    /**
     * Returns a list of errors in the error helper.
     *
     * @return array
     */
    public function getList()
    {
        return $this->errors;
    }

    /**
     * Returns whether or not this error helper has more than one error registered within it.
     *
     * @return bool
     */
    public function has()
    {
        return count($this->errors) > 0;
    }

    /**
     * @deprecated
     */
    public function output()
    {
        $formatter = new StandardFormatter($this);
        echo $formatter->render();
    }

    /**
     * @deprecated
     */
    public function outputJSON()
    {
        $formatter = new JsonFormatter($this);
        echo $formatter->render();
    }

    public function __toString()
    {
        $formatter = new StandardFormatter($this);
        return (string) $formatter->render();
    }

    public function jsonSerialize()
    {
        $formatter = new JsonFormatter($this);
        return $formatter->asArray();
    }

    public function containsField($field)
    {
        $identifier = ($field instanceof FieldInterface) ? $field->getFieldElementName() : $field;
        foreach($this->getList() as $error) {
            $field = $error->getField();
            if (is_object($field) && $field->getFieldElementName() == $identifier) {
                return true;
            }
        }
        return false;
    }

}
