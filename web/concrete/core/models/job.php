<?

defined('C5_EXECUTE') or die("Access Denied.");

/**
*
* Contains the job class.
* @package Utilities
* @author Andrew Embler <andrew@concrete5.org>
* @author Tony Trupp <tony@concrete5.org>
* @link http://www.concrete5.org
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/

/**
*
* The job class is essentially sub-dispatcher for certain maintenance tasks that need to be run at specified intervals. Examples include indexing a search engine or generating a sitemap page.
* @package Utilities
* @author Andrew Embler <andrew@concrete5.org>
* @author Tony Trupp <tony@concrete5.org>
* @link http://www.concrete5.org
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/
class Concrete5_Model_Job extends Object {

	//Required Job Variables - Override these variables in your child job class
	public $jName="Job base class";
	public $jDescription="";

	//you must override this method
	function run(){
		throw new Exception(t('Error: The Job::run() method must be overridden by your child Job class.'));
	}	
	
	public function getJobName() {return $this->jName;}
	public function getJobDescription() {return $this->jDescription;}	
	public function getJobHandle() {return $this->jHandle;}
	public function getJobID() {return $this->jID;}
	public function getPackageHandle() {
		return PackageList::getHandle($this->pkgID);
	}
	
	
	//==========================================================
	// JOB MANAGEMENT - do not override anything below this line 
	//==========================================================
	
	
	//meta variables
	public $errors=array();
	protected $jobClassLocations=array();
	
	//Other Job Variables
	public $jID=0;
	public $jStatus='ENABLED';	
	public $availableJStatus=array( 'ENABLED','RUNNING','DISABLED_ERROR','DISABLED' );
	public $jDateLastRun=' default ';	
	public $jHandle='';
	public $jNotUninstallable=0;
	
	/*
	final public __construct(){
		//$this->jHandle="example_job_file.php";		
	}
	*/
	
	public static function jobClassLocations(){
		return array(DIR_FILES_JOBS, DIR_FILES_JOBS_CORE);
	}


	// authenticateRequest checks against your site's salt and a custom auth field to make 
	// sure that this is a request that is coming either from something cronned by the site owner
	// or from the dashboard
	public static function authenticateRequest($auth) {
		$val = PASSWORD_SALT . ':' . DIRNAME_JOBS;
		return md5($val) == $auth;
	}
	
	public static function generateAuth() {
		$val = PASSWORD_SALT . ':' . DIRNAME_JOBS;
		return md5($val);
	}
	
	public static function exportList($xml) {
		$jl = self::getList();
		if ($jl->numRows() > 0) {
			$jx = $xml->addChild('jobs');
			while($r = $jl->FetchRow()) {
				$j = Job::getByID($r['jID']);
				$ch = $jx->addChild('job');
				$ch->addAttribute('handle',$j->getJobHandle());
				$ch->addAttribute('package',$j->getPackageHandle());
			}
		}	
	}

	// Job Retrieval 
	// ==============
	
	public static function getList(){
		$db = Loader::db();
		$v=array();
		$q = "SELECT * FROM Jobs ORDER BY jDateLastRun";
		$rs = $db->query($q, $v);
		return $rs;
	}
	
	public static function resetRunningJobs() {
		$db = Loader::db();
		$db->Execute('update Jobs set jStatus = \'ENABLED\' where jStatus = \'RUNNING\'');
	}
	
	public static function getByID( $jID=0 ){
		$db = Loader::db(); 
		$jobData = $db->getRow("SELECT * FROM Jobs WHERE jID=".intval($jID));
		if( !$jobData || !$jobData['jHandle']  ) return NULL; 
		return Job::getJobObjByHandle( $jobData['jHandle'], $jobData );
	}
	
	public static function getByHandle( $jHandle='' ){
		$db = Loader::db(); 
		$jobData = $db->getRow( 'SELECT * FROM Jobs WHERE jHandle=?', array($jHandle) );
		if( !$jobData || !$jobData['jHandle']  ) return NULL; 
		return Job::getJobObjByHandle( $jobData['jHandle'], $jobData );
	}
	
	public static function getJobObjByHandle( $jHandle='', $jobData=array() ){
		$jcl = Job::jobClassLocations();
		
		//check for the job file in the various locations
		$db = Loader::db();
		$pkgID = $db->GetOne('select pkgID from Jobs where jHandle = ?', $jHandle);
		if ($pkgID > 0) {
			$pkgHandle = PackageList::getHandle($pkgID);
			if ($pkgHandle) {
				
				$jcl[] = DIR_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_JOBS;
				$jcl[] = DIR_PACKAGES_CORE . '/' . $pkgHandle . '/' . DIRNAME_JOBS;
				
			}
		}

		foreach( $jcl as $jobClassLocation ){
			//load the file & class, then run the job
			$path=$jobClassLocation.'/'.$jHandle.'.php';	
			if( file_exists($path) ){ 
				require_once($path);
				$className=Object::camelcase( $jHandle );
				$j = new $className();
				$j->jHandle=$jHandle;
				if(intval($jobData['jID'])>0){
					$j->jID=intval($jobData['jID']);
					$j->jStatus=$jobData['jStatus'];
					$j->jDateLastRun=$jobData['jDateLastRun'];
					$j->jLastStatusText=$jobData['jLastStatusText'];					
					$j->pkgID=$jobData['pkgID'];
					$j->jDateInstalled=$jobData['jDateInstalled'];	
					$j->jNotUninstallable=$jobData['jNotUninstallable'];			
				}
				return $j;
			}
		}
		
		return NULL;
	}
	
	//Scan job directories for job classes
	public static function getAvailableList($includeConcreteDirJobs=1){
	
		$jobObjs=array(); 
	
		//get existing jobs
		$existingJobHandles=array();
		$existingJobsRS = Job::getList();
		while($existingJobsRow = $existingJobsRS->fetchRow() ) 
			$existingJobHandles[]=$existingJobsRow['jHandle'];
	
		if(!$includeConcreteDirJobs)
			 $jobClassLocations = array( DIR_FILES_JOBS );
		else $jobClassLocations = Job::jobClassLocations();
	
		foreach( $jobClassLocations as $jobClassLocation){ 
			// Open a known directory, and proceed to read its contents
			if (is_dir($jobClassLocation)) {
				if ($dh = opendir($jobClassLocation)) {
					while (($file = readdir($dh)) !== false) {
						if( substr($file,strlen($file)-4)!='.php' ) continue;
						
						$alreadyInstalled=0;
						foreach($existingJobHandles as $existingJobHandle){
							if( substr($file,0,strlen($file)-4)==$existingJobHandle){
								$alreadyInstalled=1;
								break;
							}
						}
						if($alreadyInstalled) continue;
						
						$path=$jobClassLocation .'/'. $file;
						require_once( $jobClassLocation .'/'. $file );
						$jHandle = substr($file,0,strlen($file)-4);
						$className=Object::camelcase( $jHandle );
						if(class_exists($className)){
							$jobObjs[$jHandle]=new $className();
							$jobObjs[$jHandle]->jHandle=$jHandle;
							if(!$jobObjs[$jHandle] instanceof Job){
								$jobObjs[$jHandle]->jDescription= t('Error: The Job class must be a child class of Job.');
								$jobObjs[$jHandle]->invalid=1;
							}
						  }else{	
						  	$invalidJob = new Job();
							$invalidJob->jName = $className;
							$invalidJob->jHandle=$jHandle;
							$invalidJob->jDescription = t('Error: Invalid Job file. The class %s was not found in %s .', $className, $path); 
							$invalidJob->invalid=1;
							$jobObjs[$jHandle] = $invalidJob;
						}					
					}
					closedir($dh);
				}
			}
		}
		
		return $jobObjs;
	}

	
	
	// Running Jobs
	// ==============

	public static function runAllJobs(){
		//loop through all installed jobs
		$jobListRS=Job::getList();
		while( $jobItem = $jobListRS->fetchRow() ){ 
			$jobObj = Job::getJobObjByHandle($jobItem['jHandle']);
			$jobObj->executeJob();
		}
	}
	
	public function executeJob(){
		Events::fire('on_before_job_execute', $this);
		$db = Loader::db();
		$timestampH =date('Y-m-d g:i:s A');
		$timestamp=date('Y-m-d H:i:s');
		$this->jDateLastRun = $timestampH; 
		$rs = $db->query( "UPDATE Jobs SET jStatus='RUNNING', jDateLastRun=? WHERE jHandle=?", array( $timestamp, $this->jHandle ) );
		try{ 
			$resultMsg=$this->run();
			if(strlen($resultMsg)==0) 
				$resultMsg= t('The Job was run successfully.');
		}catch(Exception $e){
			$resultMsg=$e->getMessage();
			$this->loadError(2);
		}
		
		if( !$this->isError() ) $jStatus='ENABLED';
		else $jStatus='DISABLED_ERROR';
		$rs = $db->query( "UPDATE Jobs SET jStatus=?, jLastStatusText=? WHERE jHandle=?", array( $jStatus, $resultMsg, $this->jHandle ) );
		
		$enum = 0;
		if ($this->getError() > 0) {
			$enum = $this->getError();
		}
		$rs = $db->query( "INSERT INTO JobsLog (jID, jlMessage, jlTimestamp, jlError) VALUES(?,?,?,?)", array( $this->jID, $resultMsg, $timestamp, $enum ) );
		Events::fire('on_job_execute', $this);
		
		return $resultMsg;
	}

	public function setJobStatus($jStatus='ENABLED'){
		$db = Loader::db();
		if( !in_array($jStatus,$this->availableJStatus) )
			$jStatus='ENABLED';
		$rs = $db->query( "UPDATE Jobs SET jStatus=? WHERE jHandle=?", array( $jStatus, $this->jHandle ) );
	}
 
 	 public function installByHandle($jHandle=''){
		$availableJobs=Job::getAvailableList();
		foreach( $availableJobs as $availableJobHandle=>$availableJobObj ){
			if( $availableJobObj->jHandle!=$jHandle ) continue;
			$availableJobObj->install();
		}
	}

	public static function getListByPackage($pkg) {
		$db = Loader::db();
		$list = array();
		$r = $db->Execute('select jHandle from Jobs where pkgID = ? order by jHandle asc', array($pkg->getPackageID()));
		while ($row = $r->FetchRow()) {
			$list[] = Job::getJobObjByHandle($row['jHandle']);
		}
		$r->Close();
		return $list;
	}	
	
	
	public function installByPackage($jHandle, $pkg) {
		$dir = is_dir(DIR_PACKAGES . '/' . $pkg->getPackageHandle()) ? DIR_PACKAGES . '/' . $pkg->getPackageHandle() : DIR_PACKAGES_CORE . '/' . $pkg->getPackageHandle();
		require_once( $dir .'/'. DIRNAME_JOBS . '/' . $jHandle . '.php');
		$className=Object::camelcase( $jHandle  );
		if(class_exists($className)){
			$j = new $className();
			$db = Loader::db();
			$db->Execute('insert into Jobs (jName, jDescription, jDateInstalled, jNotUninstallable, jHandle, pkgID) values (?, ?, ?, ?, ?, ?)', 
				array($j->getJobName(), $j->getJobDescription(), Loader::helper('date')->getLocalDateTime(), 0, $jHandle, $pkg->getPackageID()));
			Events::fire('on_job_install', $j);
			return $j;
		}
	}
 
	public function install(){
		
		$db = Loader::db();
		$jobExists=$db->getOne( 'SELECT count(*) FROM Jobs WHERE jHandle=?', array($this->jHandle) );
		$vals=array($this->getJobName(),$this->getJobDescription(),  date('Y-m-d H:i:s'), $this->jNotUninstallable, $this->jHandle);
		if($jobExists){
			$db->query('UPDATE Jobs SET jName=?, jDescription=?, jDateInstalled=?, jNotUninstallable=? WHERE jHandle=?',$vals);
		}else{
			$db->query('INSERT INTO Jobs (jName, jDescription, jDateInstalled, jNotUninstallable, jHandle) VALUES(?,?,?,?,?)',$vals);
		}
		Events::fire('on_job_install', $this);
	}
 
	public function uninstall(){
		$ret = Events::fire('on_job_uninstall', $this);
		if($ret < 0) {
			return $ret;
		}
		$db = Loader::db();
		$db->query( 'DELETE FROM Jobs WHERE jHandle=?', array($this->jHandle) );
	}
	
	/** 
	 * Removes Job log entries 
	 */
	public static function clearLog() {
		$db = Loader::db();
		$db->Execute("delete from JobsLog");
	}

}

?>