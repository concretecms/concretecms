<?php

namespace Concrete\Core\Support\ServiceProvider;

use Core;

class MetadataGenerator
{
    public function render()
    {
        $methodTypes = array();

        $bindings = Core::getBindings();
        foreach ($bindings as $name => $binding) {
            /** @var \Closure $binding */
            $reflection = new \ReflectionFunction($binding['concrete']);
            $static = $reflection->getStaticVariables();
            if (!isset($static['concrete'])) {
                try {
                    $class = Core::make($name);
                    $methodTypes[$name] = get_class($class);
                } catch (\Exception $e) {}
            } else {
                $methodTypes[$name] = $static['concrete'];
            }
        }

        $file = '<?php namespace PHPSTORM_META { $STATIC_METHOD_TYPES = array(\\Core::make(\'\') => array(';
        foreach ($methodTypes as $name => $class) {
            $file .= '\'' . $name . '\' instanceof ' . $class . ',';
        }
        $file .= '));}';

        return $file;
    }
}