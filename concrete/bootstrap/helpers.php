<?php
defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Utility\Service\Text;
use \Concrete\Core\Localization\Localization;

/**
 * Translate text (simple form).
 *
 * @param string $text The text to be translated.
 * @param        mixed ... Unlimited optional number of arguments: if specified they'll be used for printf.
 *
 * @return string Returns the translated text.
 *
 * @example t('Hello %s') will return translation for 'Hello %s' (example for Italian 'Ciao %s').
 * @example t('Hello %s', 'John') will return translation for 'Hello %s' (example: 'Ciao %s'), using 'John' for printf (so the final result will be 'Ciao John' for Italian).
 */
function t($text)
{
    $loc = Localization::getInstance();
    $adapter = $loc->getActiveTranslatorAdapter();
    $args = func_get_args();
    switch (count($args)) {
        case 1:
            return $adapter->translate($text);
        case 2:
            return $adapter->translate($text, $args[1]);
        case 3:
            return $adapter->translate($text, $args[1], $args[2]);
        case 4:
            return $adapter->translate($text, $args[1], $args[2], $args[3]);
        default:
            return call_user_func_array(array($adapter, 'translate'), $args);
    }
}

/**
 * Translate text (plural form).
 *
 * @param string $singular The singular form.
 * @param string $plural   The plural form.
 * @param int    $number   The number.
 * @param        mixed     ... Unlimited optional number of arguments: if specified they'll be used for printf
 *
 * @return string Returns the translated text.
 *
 * @example t2('%d child', '%d children', $n) will return translated '%d child' if $n is 1, translated '%d children' otherwise.
 * @example t2('%d child', '%d children', $n, $n) will return translated '1 child' if $n is 1, translated '2 children' if $n is 2.
 */
function t2($singular, $plural, $number)
{
    $loc = Localization::getInstance();
    $adapter = $loc->getActiveTranslatorAdapter();
    $args = func_get_args();
    switch (count($args)) {
        case 3:
            return $adapter->translatePlural($singular, $plural, $number);
        case 4:
            return $adapter->translatePlural($singular, $plural, $number, $args[3]);
        case 5:
            return $adapter->translatePlural($singular, $plural, $number, $args[3], $args[4]);
        case 6:
            return $adapter->translatePlural($singular, $plural, $number, $args[3], $args[4], $args[5]);
        default:
            return call_user_func_array(array($adapter, 'translatePlural'), $args);
    }
}

/**
 * Translate text (simple form) with a context.
 *
 * @param string $context A context, useful for translators to better understand the meaning of the text to be translated.
 * @param string $text    The text to be translated.
 * @param        mixed    ... Unlimited optional number of arguments: if specified they'll be used for printf.
 *
 * @return string Returns the translated text.
 *
 * @example tc('Recipient', 'To %s') will return translation for 'To %s' (example for Italian 'A %s').
 * @example tc('End date', 'To %s') will return translation for 'To %s' (example for Italian 'Fino al %s').
 * @example tc('Recipient', 'To %s', 'John') will return translation for 'To %s' (example: 'A %s'), using 'John' for printf (so the final result will be 'A John' for Italian).
 * @example tc('End date', 'To %s', '01/01/2000') will return translation for 'To %s' (example: 'Fino al %s'), using '01/01/2000' for printf (so the final result will be 'Fino al 01/01/2000' for Italian).
 */
function tc($context, $text)
{
    $loc = Localization::getInstance();
    $adapter = $loc->getActiveTranslatorAdapter();
    $args = func_get_args();
    switch (count($args)) {
        case 2:
            return $adapter->translateContext($context, $text);
        case 3:
            return $adapter->translateContext($context, $text, $args[2]);
        case 4:
            return $adapter->translateContext($context, $text, $args[2], $args[3]);
        case 5:
            return $adapter->translateContext($context, $text, $args[2], $args[3], $args[4]);
        default:
            return call_user_func_array(array($adapter, 'translateContext'), $args);
    }
}

/**
 * Security helper.
 *
 * @param string $input
 *
 * @return string mixed
 */
function h($input)
{
    return id(new Text())->specialchars($input);
}

/**
 * Class member access on instantiation.
 *
 *     id(new Block)->render();
 *
 * @param  mixed $mixed
 *
 * @return mixed mixed
 */
function id($mixed)
{
    return $mixed;
}

/**
 *  Returns a concrete5 namespaced class. $prefix is either true (for application), or a package handle or null.
 *
 * @param string $class
 * @param bool   $prefix
 *
 * @return string
 */
function core_class($class, $prefix = false)
{
    $app = \Core::make('app');
    $class = trim($class, '\\');
    if ($prefix) {
        if (substr($class, 0, 5) == "Core\\") {
            if ($prefix !== true) {
                $x = $app->make('Concrete\Core\Package\PackageService')->getClass($prefix);
                if (!$x->shouldEnableLegacyNamespace()) {
                    $class = substr($class, 5);
                } else {
                    $class = "Src\\" . substr($class, 5);
                }
            } else {
                if (!Config::get('app.enable_legacy_src_namespace')) {
                    $class = "Concrete\\" . substr($class, 5);
                } else {
                    $class = "Src\\" . substr($class, 5);
                }
            }
        }

        if ($prefix === true) {
            $prefix = Config::get('app.namespace');
        } else {
            $prefix = 'Concrete\\Package\\' . camelcase($prefix);
        }
    }

    if (!$prefix) {
        $prefix = 'Concrete';
    }

    $class = '\\' . $prefix . '\\' . $class;

    return $class;
}

function overrideable_core_class($class, $path, $pkgHandle = null)
{
    $env = \Environment::get();

    // First, check to see if the class we're trying to override is in the Core namespace
    if (substr($class, 0, 5) == "Core\\") {
        // If so, we first check to see if application/src/Concrete/This/Stuff exists
        // So let's strip DIRNAME_CLASSES off the front, place /Concrete/ between DIRNAME_CLASSES
        // and the rest of the path.
        $newPath = substr($path, strlen(DIRNAME_CLASSES));
        $newPath = DIRNAME_CLASSES . '/Concrete' . $newPath;
        $r = $env->getRecord($newPath);
        if ($r->override) {
            return core_class($class, true);
        }
    }

    $r = $env->getRecord($path);
    $prefix = $r->override ? true : $pkgHandle;

    return core_class($class, $prefix);

}

/**
 * Returns $string in CamelCase.
 *
 * @param string $string
 * @param bool   $leaveSlashes
 *
 * @return string
 */
function camelcase($string, $leaveSlashes = false)
{
    $return = '';
    $string = trim($string, '_-/\\');
    if (strpos($string, '/')) {
        $segments = explode('/', $string);
        foreach ($segments as $segment) {
            $subsegments = preg_split("/[_-]/", $segment);
            foreach ($subsegments as $subsegment) {
                $return .= ucfirst($subsegment);
            }
            if ($leaveSlashes) {
                $return .= '/';
            }
        }
        $return = trim($return, '/');
    } else {
        $segments = preg_split("/[_-]/", $string);
        foreach ($segments as $segment) {
            $return .= ucfirst($segment);
        }
    }

    return $return;
}

/**
 * Returns CamelCase string as camel_case.
 *
 * @param  string $string
 *
 * @return string mixed
 */
function uncamelcase($string)
{
    $v = preg_split('/([A-Z])/', $string, false, PREG_SPLIT_DELIM_CAPTURE);
    $a = array();
    array_shift($v);
    for ($i = 0; $i < count($v); ++$i) {
        if ($i % 2) {
            if (function_exists('mb_strtolower')) {
                $a[] = mb_strtolower($v[$i - 1] . $v[$i], APP_CHARSET);
            } else {
                $a[] = strtolower($v[$i - 1] . $v[$i]);
            }
        }
    }

    return str_replace('__', '_', implode('_', $a));
}

/**
 * Fills an object properties from an array.
 */
/**
 * @param $o
 * @param $array
 *
 * @return mixed
 */
function array_to_object($o, $array)
{
    foreach ($array as $property => $value) {
        $o->$property = $value;
    }

    return $o;
}

/**
 * Dumps information about a variable in a way that can be used with Doctrine recursive objects.).
 *
 * @param $o
 * @param bool $maxDepth
 */
function var_dump_safe($o, $echo = true, $maxDepth = true)
{
    return Doctrine\Common\Util\Debug::dump($o, $maxDepth, true, $echo);
}

/**
 * Generate the PHPDoc for a set of defined variables.
 *
 * @param array $get_defined_vars The result of the get_defined_vars() function
 * @param object|null $valueOfThis The value of $this
 * @param bool $return Set to true to return the generated PHPDoc, false to return it
 *
 * @example output_vars(get_defined_vars(), isset($this) ? $this : null);
 */
function output_vars(array $get_defined_vars, $valueOfThis = null, $return = false)
{
    if (!array_key_exists('this', $get_defined_vars) && $valueOfThis && is_object($valueOfThis)) {
        $get_defined_vars['this'] = $valueOfThis;
    }
    $generator = new Concrete\Core\Support\Symbol\PhpDocGenerator();
    $phpDoc = $generator->describeVars($get_defined_vars);
    if (!$return) {
        echo '</script><pre>', $phpDoc;
        die();
    }

    return $phpDoc;
}
