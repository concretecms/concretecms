<?

$config_check_failed = false;

if (version_compare(PHP_VERSION, '5.0.0', '<')) {
	die("Concrete5 requires PHP 5.0.0 or higher.");
}

if (!@include('config/site.php')) {
	// nothing is installed
	$config_check_failed = true;
}