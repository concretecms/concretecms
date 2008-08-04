<?

# ------------------------------------------------ #
# Connect to database
# ------------------------------------------------ #

function printError(&$obj) {
	$db = Loader::db();
	$v = View::getInstance();
	$v->renderError('Database Error', $obj->getMessage());
	exit;
}

/*
$db = Loader::db();
if (!PEAR::isError($db) && is_object($db)) {
	PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, "printError");
} else {
	$v = View::getInstance();
	$v->renderError('Database Error', mysql_error());
	exit;
}
*/
?>