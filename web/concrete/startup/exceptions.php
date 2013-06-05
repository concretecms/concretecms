<?php 
defined('C5_EXECUTE') or die("Access Denied.");
function Concrete5_Exception_Handler($e) {
	// log if setup to do so
	if (ENABLE_LOG_ERRORS) {
		$db = Loader::db();
		$tables = $db->MetaTables();
		if (in_array('Logs', $tables)) {
			$l = new Log(LOG_TYPE_EXCEPTIONS, true, true);
			$l->write(t('Exception Occurred: ') . sprintf("%s:%d %s (%d)\n", $e->getFile(), $e->getLine(), $e->getMessage(), $e->getCode()));
			$l->write($e->getTraceAsString());
			$l->close();
		}
	}

	if (Config::get('SITE_DEBUG_LEVEL') == DEBUG_DISPLAY_ERRORS) {
		View::renderError(t('An unexpected error occurred.'), $e->getMessage(), $e);		
	} else {
		View::renderError(t('An unexpected error occurred.'), t('An error occurred while processing this request.'), $e);
	}
}

set_exception_handler('Concrete5_Exception_Handler');
