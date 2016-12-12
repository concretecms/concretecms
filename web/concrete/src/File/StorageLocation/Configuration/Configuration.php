<?php
namespace Concrete\Core\File\StorageLocation\Configuration;

class Configuration
{

    public function getTypeObject()
    {

        $class = substr(get_called_class(), strrpos(get_called_class(), '\\') + 1);
        $class = substr($class, 0, strpos($class, 'Configuration'));
        $handle = uncamelcase($class);
        return \Concrete\Core\File\StorageLocation\Type\Type::getByHandle($handle);
    }
}
