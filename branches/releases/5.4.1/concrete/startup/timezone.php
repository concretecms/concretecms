<?php 	defined('C5_EXECUTE') or die("Access Denied.");
if(defined('APP_TIMEZONE')) {
	define('APP_TIMEZONE_SERVER', @date_default_timezone_get());
	date_default_timezone_set(APP_TIMEZONE);
} else {
	date_default_timezone_set(@date_default_timezone_get());
}
?>