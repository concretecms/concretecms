<?

defined('C5_EXECUTE') or die("Access Denied.");

if (!ini_get('safe_mode')) {
	@set_time_limit(0);
}

$json = Loader::helper('json');
$r = new stdClass;
$r->results = array();

if (Job::authenticateRequest($_REQUEST['auth'])) {

	// Legacy 
	if ($_REQUEST['jID']) {
		$j = Job::getByID($_REQUEST['jID']);
		$obj = $j->executeJob();
		print $json->encode($obj);
		exit;
	}

	if ($_REQUEST['jsID']) {
		$js = JobSet::getByID($_REQUEST['jsID']);
	} else {
		// default set legacy support
		$js = JobSet::getDefault();
	}

	if (is_object($js)) {
		$jobs = $js->getJobs();
		$js->markStarted();
		foreach($jobs as $j) {
			$obj = $j->executeJob();
			$r->results[] = $obj;
		}

		print $json->encode($r);
		exit;
	}
	
}