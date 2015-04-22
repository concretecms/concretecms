<?php

use Concrete\Core\Support\Symbol\ClassSymbol\ClassSymbol;
use Concrete\Core\Support\Symbol\ClassSymbol\MethodSymbol\MethodSymbol;

define('C5_ENVIRONMENT_ONLY', true);

error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
ini_set('display_errors', 1);
define('C5_EXECUTE', true);
if (isset($argv) && is_array($argv) && isset($argv[1])) {
    define('DIR_BASE', $argv[1]);
} else {
    define('DIR_BASE', dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'web');
}
$corePath = DIR_BASE . '/concrete';
$cms = require_once $corePath . '/dispatcher.php';

$generator = new \Concrete\Core\Support\Symbol\SymbolGenerator();
$symbols = $generator->render(
    "\n",
    '    ',
    function (ClassSymbol $class, MethodSymbol $method) {
        if ($class->isFacade()) {
            return true;
        }

        return false;
    }
);
file_put_contents(DIR_BASE . '/concrete/src/Support/__IDE_SYMBOLS__.php', $symbols);

$metadataGenerator = new \Concrete\Core\Support\Symbol\MetadataGenerator();

$meta = $metadataGenerator->render();
file_put_contents(DIR_BASE . '/concrete/src/Support/.phpstorm.meta.php', $meta);

die("Generation Complete.\n");
