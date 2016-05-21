<?php

defined('C5_EXECUTE') or die("Access Denied.");

/*
 * ----------------------------------------------------------------------------------
 * Checks to see if magic_quotes_gpc is enabled, if so we process all the input. Blah
 * Thanks very much to the guide posted by eZ Systems:
 * http://talks.php.net/show/php-best-practices/26
 * ----------------------------------------------------------------------------------
 */

if (get_magic_quotes_gpc()) {
    $in = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
    while (list($k, $v) = each($in)) {
        foreach ($v as $key => $val) {
            if (!is_array($val)) {
                $in[$k][$key] = stripslashes($val);
                continue;
            }
            $in[] = &$in[$k][$key];
        }
    }
    unset($in);
}
