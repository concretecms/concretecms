<?php 
/**
*
* Responsible for loading the indexed search class and initiating the reindex command.
* @package Utilities 
*
* Some operational points of interest:
*
*	_emsg is set by the lowest level function to detect an error;
*	 the error logger is the 'system' trace log (not to be confused
*	 with the job logger found in the spool file)
*
*	low level functions return true if OK or false if failure
*
*	This class presumes an MD5 fingerprint file that is used to verify
*	 the integrity of the transfer
*
*	The actual writing of the inventory is done by the Distributor class which
*	 interacts between inventory and the stores through the Magento Ecommerce
*	 engine.
*
*/

define ('SPOOL_DIR','../spool/');  // Don't know where this should be
define ('PROBLEM_FILE_SUFFIX','_NG');
define ('THROW_EXCEPTIONS',0); // don't know about this yet
define ('FTP_TIMEOUT',30);
define ('FP_SUFFIX','MD5'); // suffix for file with MD5 fingerprint

defined ('C5_EXECUTE') or die(_("Access Denied."));
class LoadInventory extends Job {

	const K_BUFFER_SIZE = 1000;    // will read in 1000 lines at a time
	const K_FIELD_DELM = "|";
	protected $_ftpConn;  
	protected $_logger;   
	protected $_currency;
	protected $_errorDetails;
	protected $_lineCount;
	protected $_fieldCount;
	

	public $jNotUninstallable=1;
	
// 	Functions required by framework...

	function getJobName() {
		return t("Load Inventory");
	}
	
	function getJobDescription() {
		return t("Inventory loader.");
	}
	
	function run() {
		try {
		// sanity checks...
		if (!defined('INV_FILENAME')) throw new Exception("No inventory filename");
		if (!defined('INV_FTP_HOST')) throw new Exception("No FTP hostname found");
		if (!defined('INV_FTP_LOGIN')) throw new Exception("No FTP login");

		$inFN = INV_SPOOL_DIR."INV_".date("M_d_H_i_s").".txt";
		if (!$this->getFile($inFN)) { // could not get file from host
			$this->logError($this->_errorDetails);
			return t("Inventory read failed....".$this->_errorDetails);
		} // could not get file from host	

		$storeReference = null;   //TODO get some info for store connection
		if (buildInventory($inFN,$storeReference) == false) {
			$this->logError($this->_errorDetials);
			return t("Load inventory fail: ".$this->_errorDetails);
		}

		return "Load OK";
		}
		catch(Exception $e) {
		$this->logError($e);
		if (THROW_EXCEPTIONS) throw new Exception($e);  // send to next handler 
		return $e->getMessage();
		}
	}

//
//	Constructor simply sets up some logging
	function __construct() {
		$this->_currency = "$";  // we may need this
		$this->_logger = null;   // TODO
	}

//
//	BuildInventory - moves the inventory from the given file handle into
//	the store.
//
//	Notes:
//		Problem records are written to a 'problem file' of the
//		same filename as input file
//
	protected function buildInventory($inFN,$storeRef) { // function build inventory
		$rV = false;
		try { // try
		$inFH = fopen($inFN,"r");
		$headerLine = fgets($inFH);

		if (!(headerOK($headerLine))) { // header line not what we expected... let's not do this
			throw new Exception("Inventory header error: ".$this->_errorDetails);

		} // lets not do this

		$problemFile = fopen($inFN.PROBLEM_FILE_SUFFIX,"a");

		while (!feof($inFH)) { // while still reading lines

			$nextLine = fgets($inFH);
			$nextLineFields = explode(self::K_FIELD_DELIM,$nextLine);


		} // while still reading lines
		
		$rV = true; // all done, all OK

		} // try

	catch (Exception $e) {
		$this->logError($e);
		if (THROW_EXCEPTIONS) throw new Exception($e);  // send to next handler 
		$this->_errorDetails = $e->getMessage();
		}
		fclose($inFH);
		fclose($problemFile); 
		return $rV;

	} // function buildInventory

//
//	getHeaderFields - perhaps designed to be specialized
//	 for different file formats
//	 
	protected function getHeaderFields() {
 	return array("PIN","TITLE","AUTHOR","LIST PRICE","AVAILABLE QTY",
               	 "MIN REORDER QTY","ANSWER CODE","ANSWER TEXT");

	}
//
//		headerOK makes certain the first line matches the expected
//		 headings for our expected file
//
//		
//
	protected function headerOK($inHeader) {

	 	try {	
		$expectedFields = this->getHeaderFields();
		$this->_fieldCount = count($expectedFields);
		$receivedFields = explode(self::K_FIELD_DELIM,$inHeader);
               	if (count($receivedFields) != $this->_fieldCount))  {
		throw new Exception("Mismatch of number of header fields");
		}

		for ($i = 0; $i < count($expectedFields); $i++) {
			if ($expectedFields[$i] != trim(strtoupper($receivedFields[$i]))) {
			throw new Exception("Unexepcted header field ".$receivedFields[$i]);
			}
		}
		return true;
		}
		catch(Exception $e) {		
			$this->logError($e);
			if (THROW_EXCEPTIONS) throw new Exception($e);  // send to next handler 
			$this->_errorDetails = $e->getMessage();
			return false;
		}

	 
	}


//	logError($inString)
	private function logError($inString) {
	 	
	}


//	Inventory file transfer functions
//
//	Get the inventory file
	protected function getFile($destFileN) {
	try {
		$ftpResource = ftp_connect(INV_FTP_HOST);
        	if ($ftpResource === false)  {
			$this->_errorDetails = "Connect failed";
			return false;
		} // connect fail 

		if (!(ftp_login($ftpResource,INV_FTP_LOGIN,"kayenf@Buffalo1954"))) { // login fail
			$this->_errorDetails = t("FTP user rejected");
			ftp_close($ftpResource);
			return false;
		} // login fail

		if (!(ftp_get($ftpResource,$destFileN,INV_FILENAME,FTP_ASCII))) { // transfer NG
			$this->_errorDetails = t("FTP transfer failed ".INV_FILENAME);
			ftp_close ($ftpResource);
			return false;
		} // transfer NG		

//		Now get the file fingerprint and compare
//
		if (false) { // !!!!!!!!!!! go around for now
		if (!(ftp_get($ftpResource,$destFileN.FP_SUFFIX,
			  INV_FILENAME.FP_SUFFIX,FTP_ASCII))) { // transfer NG
			$this->_errorDetails = t("FTP fingerprint file transfer failed ".INV_FILENAME.FP_SUFFIX);
			ftp_close ($ftpResource);
			return false;
		 } // transfer NG
		} // !!!!!!!!!!!!!! go around for now


// 		All is well
		ftp_close($ftpResource);

//		Compare local fingerprint with what client sent
		$localMD5 = md5_file($destFN);
//		TODO


		return true;
	}
	catch(Exception $e) {
	}
	 
	}


}

?>
