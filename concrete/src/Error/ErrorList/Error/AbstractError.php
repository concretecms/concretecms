<?php
namespace Concrete\Core\Error\ErrorList\Error;

use Concrete\Core\Error\ErrorList\Field\FieldInterface;

abstract class AbstractError implements ErrorInterface
{

    /**
     * @var FieldInterface
     */
    protected $field;

    /**
     * @return mixed
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param mixed $field
     */
    public function setField(FieldInterface $field)
    {
        $this->field = $field;
    }

    public function __toString()
    {
        return $this->getMessage();
    }

    public function jsonSerialize()
    {
        $r = ['message' => $this->getMessage()];
        if ($this->field) {
            $r['field'] = $this->field;
        }
        return $r;
    }


}
