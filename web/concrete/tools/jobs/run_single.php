<?

defined('C5_EXECUTE') or die("Access Denied.");
if (!ini_get('safe_mode')) {
	@set_time_limit(0);
}

$json = Loader::helper('json');
if (Job::authenticateRequest($_REQUEST['auth'])) {
	if(strlen($_REQUEST['jHandle']) > 0 || intval($_REQUEST['jID']) > 0 ) {
		if($_REQUEST['jHandle']) {
			$job = Job::getByHandle($_REQUEST['jHandle']);
		} else {
			$job = Job::getByID(intval($_REQUEST['jID']));
		}
	}
}

if (is_object($job)) {
	if ($job->supportsQueue()) {

		$q = $job->getQueueObject();

		if ($_POST['process']) {
			$obj = new stdClass;
			$js = Loader::helper('json');
			try {
				$messages = $q->receive($job->getJobQueueBatchSize());
				foreach($messages as $key => $p) {
					$job->processQueueItem($p);
					$q->deleteMessage($p);
				}
				$totalItems = $q->count();	
				$obj->totalItems = $totalItems;
				if ($q->count() == 0) {
					$result = $job->finish($q);
					$obj = $job->markCompleted(0, $result);
					$obj->totalItems = $totalItems;
				}
			} catch(Exception $e) {
				$obj = $job->markCompleted(Job::JOB_ERROR_EXCEPTION_GENERAL, $e->getMessage());
				$obj->message = $obj->result; // needed for progressive library.
			}
			print $js->encode($obj);
			exit;
		} else if ($q->count() == 0) {
			$q = $job->markStarted();
			$job->start($q);
		}

		$totalItems = $q->count();
		Loader::element('progress_bar', array('totalItems' => $totalItems, 'totalItemsSummary' => t2("%d items", "%d items", $totalItems)));

		exit;

	} else {
		$r = $job->executeJob(); 	
		print $json->encode($r);
	}
} else {
	die(t('Access Denied'));
}