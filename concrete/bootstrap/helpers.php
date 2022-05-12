<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Filesystem\FileLocator;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Logging\LoggerFactory;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Utility\Service\Text;

/**
 * Translate text (simple form).
 *
 * @param string $text
 * @param mixed ...$args Unlimited optional number of arguments: if specified they'll be used for printf.
 *
 * @return string returns the translated text
 *
 * @example t('Hello %s') will return translation for 'Hello %s' (example for Italian 'Ciao %s').
 * @example t('Hello %s', 'John') will return translation for 'Hello %s' (example: 'Ciao %s'), using 'John' for printf (so the final result will be 'Ciao John' for Italian).
 */
function t($text, ...$args)
{
    $loc = Localization::getInstance();
    $adapter = $loc->getActiveTranslatorAdapter();

    return $adapter->translate($text, ...$args);
}

/**
 * Translate text (plural form).
 *
 * @param string $singular the singular form
 * @param string $plural the plural form
 * @param int $number the number
 * @param mixed ...$args Unlimited optional number of arguments: if specified they'll be used for printf
 *
 * @return string returns the translated text
 *
 * @example t2('%d child', '%d children', $n) will return translated '%d child' if $n is 1, translated '%d children' otherwise.
 * @example t2('%d child', '%d children', $n, $n) will return translated '1 child' if $n is 1, translated '2 children' if $n is 2.
 */
function t2($singular, $plural, $number, ...$args)
{
    $loc = Localization::getInstance();
    $adapter = $loc->getActiveTranslatorAdapter();

    return $adapter->translatePlural($singular, $plural, $number, ...$args);
}

/**
 * Translate text (simple form) with a context.
 *
 * @param string $context a context, useful for translators to better understand the meaning of the text to be translated
 * @param string $text the text to be translated
 * @param mixed ...$args Unlimited optional number of arguments: if specified they'll be used for printf.
 *
 * @return string returns the translated text
 *
 * @example tc('Recipient', 'To %s') will return translation for 'To %s' (example for Italian 'A %s').
 * @example tc('End date', 'To %s') will return translation for 'To %s' (example for Italian 'Fino al %s').
 * @example tc('Recipient', 'To %s', 'John') will return translation for 'To %s' (example: 'A %s'), using 'John' for printf (so the final result will be 'A John' for Italian).
 * @example tc('End date', 'To %s', '01/01/2000') will return translation for 'To %s' (example: 'Fino al %s'), using '01/01/2000' for printf (so the final result will be 'Fino al 01/01/2000' for Italian).
 */
function tc($context, $text, ...$args)
{
    $loc = Localization::getInstance();
    $adapter = $loc->getActiveTranslatorAdapter();

    return $adapter->translateContext($context, $text, ...$args);
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
    return (new Text())->specialchars($input);
}

/**
 * Class member access on instantiation.
 *
 *     id(new Block)->render();
 *
 * @param mixed $mixed
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
 * @param bool $prefix
 *
 * @return string
 */
function core_class($class, $prefix = false)
{
    $class = trim($class, '\\');
    $classPrefix = 'Concrete';
    if ($prefix) {
        $app = Application::getFacadeApplication();
        if (strpos($class, 'Core\\') === 0) {
            if ($prefix !== true) {
                $classPrefix = $prefix;
                $x = $app->make('Concrete\Core\Package\PackageService')->getClass($classPrefix);
                if (!$x->shouldEnableLegacyNamespace()) {
                    $class = substr($class, 5);
                } else {
                    $class = 'Src\\' . substr($class, 5);
                }
            } elseif (!$app['config']->get('app.enable_legacy_src_namespace')) {
                $class = 'Concrete\\' . substr($class, 5);
            } else {
                $class = 'Src\\' . substr($class, 5);
            }
        }

        if ($prefix === true) {
            $classPrefix = $app['config']->get('app.namespace');
        } else {
            $classPrefix = 'Concrete\\Package\\' . camelcase($prefix);
        }
    }

    return '\\' . $classPrefix . '\\' . $class;
}

function overrideable_core_class($class, $path, $pkgHandle = null)
{
    $app = Application::getFacadeApplication();
    $locator = $app->make(FileLocator::class);

    // First, check to see if the class we're trying to override is in the Core namespace
    if (strpos($class, 'Core\\') === 0) {
        // If so, we first check to see if application/src/Concrete/This/Stuff exists
        // So let's strip DIRNAME_CLASSES off the front, place /Concrete/ between DIRNAME_CLASSES
        // and the rest of the path.
        $newPath = substr($path, strlen(DIRNAME_CLASSES));
        $newPath = DIRNAME_CLASSES . '/Concrete' . $newPath;
        $r = $locator->getRecord($newPath);
        if ($r->override) {
            return core_class($class, true);
        }
    }

    $r = $locator->getRecord($path);
    $prefix = $r->override ? true : $pkgHandle;

    return core_class($class, $prefix);
}

/**
 * Returns $string in CamelCase.
 *
 * @param string $string
 * @param bool $leaveSlashes
 *
 * @return string
 */
function camelcase($string, $leaveSlashes = false)
{
    $string = (string) $string;
    $return = '';
    $string = trim($string, '_-/\\');
    if (strpos($string, '/')) {
        $segments = explode('/', $string);
        foreach ($segments as $segment) {
            $subsegments = preg_split('/[_-]/', $segment);
            foreach ($subsegments as $subsegment) {
                $return .= ucfirst($subsegment);
            }
            if ($leaveSlashes) {
                $return .= '/';
            }
        }
        $return = trim($return, '/');
    } else {
        $segments = preg_split('/[_-]/', $string);
        foreach ($segments as $segment) {
            $return .= ucfirst($segment);
        }
    }

    return $return;
}

/**
 * Returns CamelCase string as camel_case.
 *
 * @param string $string
 *
 * @return string mixed
 */
function uncamelcase($string)
{
    $v = preg_split('/([A-Z])/', $string, false, PREG_SPLIT_DELIM_CAPTURE);
    $a = [];
    array_shift($v);
    foreach ($v as $i => $char) {
        if ($i % 2) {
            if (function_exists('mb_strtolower')) {
                $a[] = mb_strtolower($v[$i - 1] . $char, APP_CHARSET);
            } else {
                $a[] = strtolower($v[$i - 1] . $char);
            }
        }
    }

    return str_replace('__', '_', implode('_', $a));
}

/**
 * Fills an object properties from an array.
 *
 * @param $o
 * @param $array
 *
 * @return mixed
 */
function array_to_object($o, $array)
{
    foreach ($array as $property => $value) {
        $o->{$property} = $value;
    }

    return $o;
}

/**
 * Dumps information about a variable in a way that can be used with Doctrine recursive objects.).
 *
 * @param $o
 * @param bool $maxDepth
 * @param mixed $echo
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
    $phpDoc = $generator->setIsSingleDocBlock(true)->describeVars($get_defined_vars);
    if (!$return) {
        echo '</script><pre>', $phpDoc;
        die();
    }

    return $phpDoc;
}

/**
 * Easily logs something.
 *
 * @param mixed $channel
 * @param $message
 * @param array $context
 * @param mixed $level
 */
function core_log(
    $message,
    $level = \Monolog\Logger::DEBUG,
    $channel = \Concrete\Core\Logging\Channels::CHANNEL_APPLICATION
) {
    $logger = Core::make(LoggerFactory::class)->createLogger($channel);
    $context = [];
    if (is_array($message)) {
        $context = $message[1];
        $message = $message[0];
    }
    $logger->log($level, $message, $context);
}

/**
 * Resolve the given type from the container.
 *
 * @param string $abstract
 * @param array $parameters
 *
 * @return mixed
 */
function app($abstract = null, array $parameters = [])
{
    $app = Application::getFacadeApplication();

    if ($abstract === null) {
        return $app;
    }

    return $app->make($abstract, $parameters);
}
