<?php
namespace Concrete\Core\Express;

class BaseEntity
{

    public function setProperty($handle, $object)
    {
        $method = 'set' . camelcase($handle);
        $this->$method($object);
    }

    public function getProperty($handle)
    {
        $method = 'get' . camelcase($handle);
        return $this->$method();
    }

}
