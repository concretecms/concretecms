<?php

namespace Concrete\Core\Support\Symbol;

use Concrete\Core\Application\Application;
use Concrete\Core\Support\Facade\Application as ApplicationFacade;
use Exception;
use Throwable;

class MetadataGenerator
{
    public function getAllBindings()
    {
        $bindings = [];
        $app = ApplicationFacade::getFacadeApplication();

        foreach ($app->getBindings() as $name => $binding) {
            $className = $this->resolveAbstractToClassName($app, $name);
            if ($className !== null) {
                $bindings[$name] = $className;
            }
        }
        foreach ($app->getRegisteredAliases() as $alias) {
            if (!isset($bindings[$alias])) {
                $className = $this->resolveAbstractToClassName($app, $alias);
                if ($className !== null) {
                    $bindings[$alias] = $className;
                }
            }
        }

        return $bindings;
    }

    public function render()
    {
        $output = [
            '<?php',
            '',
            'namespace PHPSTORM_META;',
            '',
        ];

        // Define $app->build('');
        $output = array_merge($output, $this->getOverride('\Illuminate\Contracts\Container\Container::build(0)', ['' => "'@'"], '$app->build(SomeClass::class)'));

        // Define $app->make('');
        $bindings = $this->getAllBindings();
        ksort($bindings);

        $makeMethod = [
            '' => "'@'",
        ];

        foreach ($bindings as $name => $className) {
            $makeMethod[$name] = "\\{$className}::class";
        }

        $output = array_merge($output, $this->getOverride('\Illuminate\Contracts\Container\Container::make(0)', $makeMethod, '$app->make(\'something\') or $app->make(SomeClass::class)'));
        $output = array_merge($output, $this->getOverride('new \Illuminate\Contracts\Container\Container', $makeMethod, '$app[SomeClass::class]'));

        return implode("\n", $output);
    }

    /**
     * @param Application $app
     * @param string $abstract
     *
     * @return string|null
     */
    private function resolveAbstractToClassName(Application $app, $abstract)
    {
        $result = null;
        try {
            $instance = $app->make($abstract);
            if (is_object($instance)) {
                $className = get_class($instance);
                if (ltrim($abstract, '\\') !== $className) {
                    $result = $className;
                }
            }
        } catch (Exception $e) {
        } catch (Throwable $e) {
        }

        return $result;
    }

    private function getOverride($string, $makeMethod, $comment)
    {
        $output = [
            "// {$comment}",
            "override({$string}, map([",
        ];

        foreach ($makeMethod as $name => $className) {
            $output[] = "  '{$name}' => {$className},";
        }

        $output[] = ']));';
        $output[] = '';

        return $output;
    }
}
