<?php
/**
 * Common information needed by all portions of the application
 *
 * phpDocumentor :: automatic documentation generator
 * 
 * PHP versions 4 and 5
 *
 * Copyright (c) 2001-2008 Gregory Beaver
 * 
 * LICENSE:
 * 
 * This library is free software; you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General
 * Public License as published by the Free Software Foundation;
 * either version 2.1 of the License, or (at your option) any
 * later version.
 * 
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @category  ToolsAndUtilities
 * @package   phpDocumentor
 * @author    Greg Beaver <cellog@php.net>
 * @copyright 2001-2008 Gregory Beaver
 * @license   http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version   CVS: $Id: common.inc.php,v 1.15 2008/02/24 02:35:44 ashnazg Exp $
 * @filesource
 * @link      http://www.phpdoc.org
 * @link      http://pear.php.net/PhpDocumentor
 * @see       parserDocBlock, parserInclude, parserPage, parserClass
 * @see       parserDefine, parserFunction, parserMethod, parserVar
 * @since     1.0rc1
 * @todo      CS cleanup - change package to PhpDocumentor
 * @todo      CS cleanup - rename constant to TOKENIZER_EXT
 */

/* phpDocumentor version */
if ('@PEAR-DIR@' != '@'.'PEAR-DIR@') {
    /** @ignore */
    define("PHPDOCUMENTOR_VER", "@VER@");
} else {
    define("PHPDOCUMENTOR_VER", "1.4.1");
}

/* phpDocumentor URL */
define("PHPDOCUMENTOR_WEBSITE", "http://www.phpdoc.org");

// set the correct path delimiter
define('SMART_PATH_DELIMITER', DIRECTORY_SEPARATOR); 

define('tokenizer_ext', extension_loaded('tokenizer') 
    && version_compare(phpversion(), "4.3.0", ">="));

// we just replace all the \ with / so that we can just operate on /
define('PATH_DELIMITER', '/'); // set the correct path delimiter

define('PHPDOCUMENTOR_WINDOWS', substr(PHP_OS, 0, 3) == 'WIN');

define('_IN_PHP5', 
    phpversion() == '5.0.0RC1-dev' || phpversion() == '5.0.0RC2-dev' 
    || version_compare(phpversion(), '5.0.0', 'ge'));

// determine which "clone" class to set, based on PHP major version
$cloneClassDir  = 'PhpDocumentor' . DIRECTORY_SEPARATOR . 'phpDocumentor';
$cloneClassFile = 'clone.inc.php';
if ('@VER@' == '@'.'VER@') {
    // we're _not_ in a PEAR installation
    $cloneClassDir = dirname(__FILE__);
}
if (_IN_PHP5) {
    // we _are_ in PHP5
    $cloneClassFile = 'clone5.inc.php';
}
require_once $cloneClassDir . DIRECTORY_SEPARATOR . $cloneClassFile;

// make arg arrays available
if (isset($_SERVER['argv'])) {
    $argv = $_SERVER['argv'];
    $argc = $_SERVER['argc'];
}

/**
 * used in phpdoc.php and new_phpdoc.php 
 *
 * @param string $directory a directory string
 *
 * @return array an array of directory contents
 * @todo CS cleanup - rename function to PhpDocumentor_ConfigFileList
 */
function phpDocumentor_ConfigFileList($directory)
{
    $ret = array();
    if (@is_dir($directory)) {
        $ret = array();

        // thanks to Jason E Sweat (jsweat@users.sourceforge.net) for fix
        $d = @dir($directory); 

        while ($d && $entry=$d->read()) {
            $getentry = false;
            if (strcmp($entry, ".") != 0 && strcmp($entry, "..") != 0) {
                if (substr($entry, 0, 1) != ".") $getentry = true;
            }
            if ($getentry == true) {
                if (strpos($entry, '.ini'))
                if (is_file($directory . PATH_DELIMITER . $entry)) {
                    $ret[] = str_replace('.ini', '', $entry);
                }
            }
        }
        if ($d) $d->close();
    } else {
    }
    return $ret;
}


/**
 * Parse an .ini file
 * 
 * Works like {@link parse_ini_file}, except it will take a section like:
 *
 * <pre>
 * [MYVAR]
 * value1
 * value2
 * value3
 * </pre>
 *
 * and return an associative array(MYVAR => array(value1, value2, value3))
 *
 * @param string $filename         full path to the ini file
 * @param bool   $process_sections add an associative index 
 *                                 for each section [in brackets]
 *
 * @return array
 * @todo CS cleanup - rename function to PhpDocumentor_parse_ini_file
 */
function phpDocumentor_parse_ini_file($filename, $process_sections = false)
{
    $ini_array = array();
    $sec_name  = "";
    $lines     = @file($filename);
    if (!$lines) return $lines;
    foreach ($lines as $line) {
        // code by Greg Beaver, ignore comments
        if ($line[0] == ';') continue;
        $line = trim($line);
        
        if ($line == "") {
            continue;
        }
        if ($line[0] == "[" && $line[strlen($line) - 1] == "]") {
            $sec_name = substr($line, 1, strlen($line) - 2);
        } else {
            if (strpos($line, "=")) {
                $pos      = strpos($line, "=");
                $property = trim(substr($line, 0, $pos));
                // code by Greg Beaver
                if (substr($property, 0, 1) == '"' && substr($property, -1) == '"') {
                    $property = 
                        stripcslashes(substr($property, 1, count($property) - 2));
                }
                $value = trim(substr($line, $pos + 1));
                if ($value == 'false') $value = false;
                if ($value == 'true') $value = true;
                if (substr($value, 0, 1) == '"' && substr($value, -1) == '"') {
                    $value = stripcslashes(substr($value, 1, count($value) - 2));
                }
                // done additions
                
                if ($process_sections) {
                    if ($sec_name != '')
                    $ini_array[$sec_name][$property] = $value;
                    else
                    $ini_array[$property] = $value;
                } else {
                    $ini_array[$property] = $value;
                }
            } else {
                // code by Greg Beaver
                if (trim($line[0]) == ';') continue;
                if ($process_sections) {
                    $ini_array[$sec_name][] = trim($line);
                }
                // done additions
            }
        }
    }
    return $ini_array;
}


/**
 * construct an "array_key_exists()" method
 * if the runtime PHP version doesn't have one
 * 
 * @todo CS Cleanup - can't avoid "prefixed by package" error
 * @todo depend on PHP_Compat for this?
 */
if (!function_exists('array_key_exists')) {
    /** 
     * Determines if a given key exists in a given array
     *
     * @param mixed $key    key to search for
     * @param array $search the array of keys to search
     *
     * @return bool whether or not the key was found
     * @ignore
     */
    function array_key_exists($key, $search)
    {
        foreach ($search as $keys => $nul) {
            if ($key == $keys) return true;
        }
        return false;
    }
}

/**
 * construct an "is_a()" method
 * if the runtime PHP version doesn't have one
 * 
 * @todo CS Cleanup - can't avoid "prefixed by package" error
 * @todo depend on PHP_Compat for this?
 */
if (!function_exists('is_a')) {
    /**
     * Determines if one item "is" an object of the other item
     *
     * @param string $classname  the class in question
     * @param string $classquery the "is it a" class
     *
     * @return bool whether or not the class "is" one
     * @ignore
     */
    function is_a($classname, $classquery)
    {
        $father = get_parent_class($classname);
        if (strtolower($father) == strtolower($classquery)) {
            return true;
        } elseif (!empty($father)) {
            return is_a($father, $classquery);
        } else {
            return false;
        }
    }
}


/**
 * Debugging output
 *
 * @param string $s the "debug message" string to echo out
 *
 * @return void
 * @todo CS Cleanup - can't avoid "prefixed by package" error
 */
function debug($s)
{
    echo "$s\n";
}

/**
 * Returns a formatted var_dump for debugging purposes.
 *
 * @param string $s string to display
 * @param mixed  $v unlimited number of variables to display with var_dump()
 *
 * @return void
 */
function fancy_debug($s,$v)
{
    if (isset($GLOBALS['dont_debug']) && $GLOBALS['dont_debug']) return;
    debug($s."\n\n</pre><blockquote><pre>");
    var_dump($v);
    if (func_num_args()>2) {
        for ($i=2;$i<func_num_args();$i++) {
            $a = func_get_arg($i);
            // debug(" ");
            var_dump($a);
        }
    }
    debug("</pre></blockquote><pre>\n\n");
}

/**
 * Returns a lower-cased version of get_class for PHP 5
 *
 * get_class() returns case as declared in the file in PHP 5
 *
 * @param object $object the object to get the classname for
 *
 * @return string the class name of the given object
 * @todo CS cleanup - rename function to PhpDocumentor_get_class
 */
function phpDocumentor_get_class($object)
{
    return strtolower(get_class($object));
}

?>
