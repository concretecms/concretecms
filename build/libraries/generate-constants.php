<?php
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
$cms = require_once $corePath . '/dispatcher.php';

$generator = new \Concrete\Core\Support\JSConstantGenerator( $constants );
$constants = $generator->render();

$js =  "$(function(){ Concrete.const = $constants; })\n";

file_put_contents(DIR_BASE . '/concrete/js/build/core/app/concrete5-const.js', $js);

die("JS Generation Complete.\n");

