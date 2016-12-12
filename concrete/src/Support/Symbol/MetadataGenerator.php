<?php
namespace Concrete\Core\Support\Symbol;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Support\Facade\Application;

class MetadataGenerator
{

    public function getAllBindings()
    {
        $bindings = [];
        $app = Application::getFacadeApplication();

        foreach ($app->getBindings() as $name => $binding) {
            try {
                $instance = $app->make($name);
                $className = get_class($instance);

                if (ltrim($name, '\\') != ltrim($className, '\\')) {
                    $bindings[$name] = $className;
                }

            } catch (\Exception $e) {
            }
        }

        return $bindings;
    }

    public function render()
    {
        $output = [
            '<?php',
            'namespace PHPSTORM_META;',
            ''
        ];

        // Define $app->make('');
        $bindings = $this->getAllBindings();

        $makeMethod = [
            "" => "'@'"
        ];

        foreach ($bindings as $name => $className) {
            $makeMethod[$name] = "\\{$className}::class";
        }

        $output = array_merge($output, $this->getOverride('\Illuminate\Contracts\Container\Container::make(0)', $makeMethod, '$app->make(SomeClass::class)'));
        $output = array_merge($output, $this->getOverride('new \Illuminate\Contracts\Container\Container', $makeMethod, '$app[SomeClass::class]'));

        return implode("\n", $output);
    }

    private function getOverride($string, $makeMethod, $comment)
    {
        $output = [
            "// {$comment}",
            "override({$string}, map(["
        ];

        foreach ($makeMethod as $name => $className) {
            $output[] = "  '{$name}' => {$className},";
        }

        $output[] = "]));";
        $output[] = "";

        return $output;
    }

}
