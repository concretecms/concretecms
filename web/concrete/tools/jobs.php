<?

defined('C5_EXECUTE') or die("Access Denied.");

if (!ini_get('safe_mode')) {
	@set_time_limit(0);
}
$jobObj = Loader::model("job");
$json = Loader::helper('json');
$outputDisabled=0;

//JSON vars
$jsonErrorCode=0;   // 0: Successful, 1: job not found, 2: Access Denoed
$jsonMessage='';
$jsonJHandle='';
$jsonJID=0;
$jsonJDateLastRun='';

if (!Job::authenticateRequest($_REQUEST['auth'])) {
	if(!$_REQUEST['debug']) {
		die(t("Access Denied."));
	} else {
		$debug = array();
		$debug['error'] = 2;
		$debug['message'] = t("Access Denied.");
		$debug['jHandle'] = '';
		$debug['jID'] = '';
		$debug['jDateLastRun'] = '';
		die($json->encode($debug));
	}
} 

if( strlen($_REQUEST['jHandle'])>0 || intval($_REQUEST['jID'])>0 ){

	if($_REQUEST['jHandle']) 
		$job = Job::getByHandle($_REQUEST['jHandle']);
	else 
		$job = Job::getByID( intval($_REQUEST['jID']) );
		
	if(!$job){ 
		$jsonErrorCode=1;
		$jsonMessage=t('Error: Job not found');
	} else {

		if ($job->supportsQueue()) {

			$q = Queue::get('job_' . $job->getJobHandle());
			if ($_POST['process']) {
				$obj = new stdClass;
				$js = Loader::helper('json');
				$messages = $q->receive($job->getJobQueueBatchSize());
				foreach($messages as $key => $p) {
					$job->processQueueItem($p);
					$q->deleteMessage($p);
				}
				$obj->totalItems = $q->count();	
				if ($q->count() == 0) {
					$q->deleteQueue('job_' . $job->getJobHandle());
				}
				print $js->encode($obj);
				exit;
			} else if ($q->count() == 0) {
				$job->addToQueue($q);
			}

			$totalItems = $q->count();
			Loader::element('progress_bar', array('totalItems' => $totalItems, 'totalItemsSummary' => t2("%d items", "%d items", $totalItems)));

			exit;

		}


	}
} 

/*

//Uncomment to turn debugging on
//$_REQUEST['debug']=1;

//all print/echo statements are suppressed, unless the debug variable is set
if(!$_REQUEST['debug']) ob_start();


//run by job handle (aka file name) or run by job id
if( strlen($_REQUEST['jHandle'])>0 || intval($_REQUEST['jID'])>0 ){

	if($_REQUEST['jHandle']) 
		$jobObj=Job::getByHandle($_REQUEST['jHandle']);
	else 
		$jobObj=Job::getByID( intval($_REQUEST['jID']) );
		
	if(!$jobObj){ 
		$jsonErrorCode=1;
		$jsonMessage=t('Error: Job not found');
	}else{ 
	
		//Change Job Status
		if( $_REQUEST['jStatus'] ){
			$jobObj->setJobStatus( $_REQUEST['jStatus'] );
		//Run Job
		}else{
			$jsonMessage = $jobObj->executeJob(); 	
		}
		
		//set json vars
		$jsonJHandle = $jobObj->jHandle;	
		$jsonJDateLastRun = $jobObj->jDateLastRun;		
		$jsonJID=$jobObj->jID;			
		if( $jobObj->isError() ) 
			$jsonErrorCode = $jobObj->getError();		
	} 
	$runTime=date(DATE_APP_GENERIC_MDYT, strtotime($jsonJDateLastRun));
//run all jobs - default
}else{ 
	Job::runAllJobs();
	if(!$_REQUEST['debug']) 
		$outputDisabled=1;
	$jsonMessage=t('All Jobs Run Successfully');
	$runTime=date(DATE_APP_GENERIC_MDYT);
	$jsonJHandle =t('All Jobs');	
}

//all print/echo statements are suppressed, unless the debug variable is set
if(!$_REQUEST['debug']){ 
	ob_end_clean();
}

//$runTime=strtotime($jsonJDateLastRun);
if(!$outputDisabled) {
	$debug = array();
	$debug['error'] = intval($jsonErrorCode);
	$debug['message'] = $jsonMessage;
	$debug['jHandle'] = $jsonJHandle;
	$debug['jID'] = $jsonJID;
	$debug['jDateLastRun'] = $runTime;
	echo $json->encode($debug);
}
*/
