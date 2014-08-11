<?php
define('FILE_PERMISSIONS_MODE', 0777);
define('DIRECTORY_PERMISSIONS_MODE', 0777);
define('C5_ENVIRONMENT_ONLY', true);

error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
ini_set('display_errors', 1);
define('C5_EXECUTE', true);
if(isset($argv) && is_array($argv) && isset($argv[1])) {
    define('DIR_BASE', $argv[1]);
}
else {
    define('DIR_BASE', dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'web');
}
$corePath = DIR_BASE . '/concrete';

require $corePath . '/bootstrap/configure.php';
require $corePath . '/bootstrap/autoload.php';
$cms = require $corePath . '/bootstrap/start.php';

error_reporting('E_ALL');
$generator = new \Concrete\Core\Support\Symbol\SymbolGenerator();

$symbols = $generator->render();
file_put_contents(DIR_BASE . '/concrete/src/Support/__IDE_SYMBOLS__.php', $symbols);
echo "Generation Complete.\n";
die(0);

