<?

$config_check_failed = false;

if (!@include('config/site.php')) {
	// nothing is installed
	$config_check_failed = true;
}