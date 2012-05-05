<?

defined('C5_EXECUTE') or die("Access Denied.");

/**
* workaround until update to PHP 5.3 takes place
* (Don't make more than one call in the same line, or it will break!!!).
*/
if (!function_exists('get_called_class')):
function get_called_class()
{
    $bt = debug_backtrace();
    $l = count($bt) - 1;
    $matches = array();
    while(empty($matches) && $l > -1){
        $lines = file($bt[$l]['file']);
        $callerLine = $lines[$bt[$l]['line']-1];
        preg_match('/([a-zA-Z0-9\_]+)::'.$bt[$l--]['function'].'/',
        $callerLine,
        $matches);
    }
    if (!isset($matches[1])) $matches[1]=NULL; //for notices
    if ($matches[1] == 'self') {
        $line = $bt[$l]['line']-1;
        while ($line > 0 && strpos($lines[$line], 'class') === false) {
            $line--;
        }
        preg_match('/class[\s]+(.+?)[\s]+/si', $lines[$line], $matches);
    }
    return $matches[1];
}
endif;