<?php

namespace Concrete\Core\File\ExternalFileProvider\Configuration;

use Concrete\Core\File\ExternalFileProvider\Type\Type;

class Configuration
{
    public function getTypeObject()
    {
        $class = substr(get_called_class(), strrpos(get_called_class(), '\\') + 1);
        $class = substr($class, 0, strpos($class, 'Configuration'));
        $handle = uncamelcase($class);

        return Type::getByHandle($handle);
    }
}
