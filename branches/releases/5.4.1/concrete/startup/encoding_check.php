<?php 

defined('C5_EXECUTE') or die("Access Denied.");

/**
 * Sets up encoding
 */
 
if (function_exists('mb_internal_encoding') && defined('APP_CHARSET')) {
	mb_internal_encoding(APP_CHARSET);
}