<?php

namespace Concrete\Core\Support\Symbol;

use Core;

class MetadataGenerator
{
    public function render()
    {
        $file = '<?php namespace PHPSTORM_META { $STATIC_METHOD_TYPES = array(\\Core::make(\'\') => array(' . PHP_EOL;

        $bindings = Core::getBindings();
        foreach ($bindings as $name => $binding) {
            /** @var \Closure $binding */
            $reflection = new \ReflectionFunction($binding['concrete']);
            $static = $reflection->getStaticVariables();
            $className = null;
            if (!isset($static['concrete'])) {
                try {
                    $class = Core::make($name);
                    $className = get_class($class);
                } catch (\Exception $e) {}
            } else {
                $className = $static['concrete'];
            }

            if ($className !== null && $className !== get_class($this)) {
                if ($className[0] !== '\\') {
                    $className = '\\' . $className;
                }

                $file .= '\'' . $name . '\' instanceof ' . $className . ',' . PHP_EOL;
            }
        }
        $file .= '));}';

        return $file;
    }
}