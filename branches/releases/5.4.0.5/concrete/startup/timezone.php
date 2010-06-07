<?php 	defined('C5_EXECUTE') or die(_("Access Denied."));
if(defined('APP_TIMEZONE')) {
	date_default_timezone_set(APP_TIMEZONE);
} else {
	date_default_timezone_set(@date_default_timezone_get());
}
?>