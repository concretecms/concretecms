<?php

namespace Concrete\Core\Support\ServiceProvider;

use Core;

class MetadataGenerator
{
    public function render()
    {
        $file = '<?php namespace PHPSTORM_META { $STATIC_METHOD_TYPES = array(\\Core::make(\'\') => array(';

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

            if ($className !== null) {
                if ($className[0] !== '\\') {
                    $className = '\\' . $className;
                }

                $file .= '\'' . $name . '\' instanceof ' . $className . ',';
            }
        }
        $file .= '));}';

        return $file;
    }
}