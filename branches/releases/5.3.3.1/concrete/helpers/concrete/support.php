<?php 
/**
 * @access private
 * @package Helpers
 * @category Concrete
 * @author Tony Trupp <tony@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */ 

defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::helper('json');

class ConcreteSupportHelper {  

	function askQuestion( $question='' ) {
		$answers=array();
		
		//diagnositic data
		$data=ConcreteSupportHelper::getDiagnosticData();
		$data['keywords']=$question;
		$data['format']='JSON';
		$postStr = http_build_query($data, '', '&'); 
		
		if (function_exists('curl_init')) {
			$curl_handle = curl_init();
			curl_setopt($curl_handle, CURLOPT_URL, KNOWLEDGE_BASE_URL);
			curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl_handle, CURLOPT_POST, true);
			curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $postStr);
			$response = curl_exec($curl_handle);
		}else{
			throw new Exception(t('php cUrl must be enabled on your server'));
		}
		
		//echo $response;
		
		if( strlen($response) ) $json = JsonHelper::decode($response);
		if( is_array($json) ) return $json; 
		return false;
	}
	
	function postQuestion( $ticketData=array() ){ 
	
		$authData = UserInfo::getAuthData();
		$ticketData=array_merge($ticketData,$authData);		 
		$postStr = http_build_query($ticketData, '', '&');
		
		if (function_exists('curl_init')) {
			$curl_handle = curl_init();
			curl_setopt($curl_handle, CURLOPT_URL, KNOWLEDGE_BASE_POST_URL);
			curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl_handle, CURLOPT_POST, true);
			curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $postStr);			
			$response = curl_exec($curl_handle);
		}else{
			throw new Exception(t('php cUrl must be enabled on your server'));
		}
		
		//echo $response;
		
		if( strlen($response) ) return JsonHelper::decode($response);
		else return array();		
	}
	
	function usersTickets(){
		Loader::model('userinfo');
		$postData = UserInfo::getAuthData(); 
		$postStr = http_build_query($postData, '', '&');
		$myTicketsURL=KNOWLEDGE_BASE_TICKET_LIST_URL;
		if (function_exists('curl_init')) {
			$curl_handle = curl_init();
			curl_setopt($curl_handle, CURLOPT_URL, $myTicketsURL);
			curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl_handle, CURLOPT_POST, true);
			curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $postStr);			
			$response = curl_exec($curl_handle);
		}else{
			throw new Exception(t('php cUrl must be enabled on your server'));
		}
		
		//echo $response;

		if( strlen($response) ){
			$responseObj=JsonHelper::decode($response); 
			//echo 'lastReplyTime:'.$responseObj->lastReplyTime.'<br>';
			//echo 'c5orgTime:'.$responseObj->c5orgTime.'<br>';
			//echo 'time diff:'.($responseObj->c5orgTime-time()).'<br>';
			//echo 'local reply time:'.($responseObj->lastReplyTime-($responseObj->c5orgTime-time())).'<br>';
			if( $responseObj->lastReplyTime ){ 
				$_SESSION['lastHelpReplyTime']=$responseObj->lastReplyTime-($responseObj->c5orgTime-time());
			}
			return $responseObj;
		}else return new Object(); 
	}
	
	function hasNewHelpResponse(){
	
		//user hasn't checked their tickets since it was noticed they have a new reply 
		if($_SESSION['newHelpResponseWaiting']==1) return 1; 
		
		$authData = UserInfo::getAuthData();
		if( $authData['auth_token'] ){
			
			//only check every 5 minutes
			if( intval($_SESSION['lastHelpWaitingCheckTime'])<(time()-300) ){
				if(UserInfo::isRemotelyLoggedIn()){
					$response=ConcreteSupportHelper::usersTickets();				
					$_SESSION['lastHelpWaitingCheckTime']=time(); 
				}
			}
			
			//echo 'TESTING: '.(intval($_SESSION['lastHelpReplyTime']).'-'.intval($_SESSION['lastSupportListViewTime']));
			//echo '='.(intval($_SESSION['lastHelpReplyTime'])-intval($_SESSION['lastSupportListViewTime']));
			
			//since last time the user viewed the tickets list, or since last login
			if( (intval($_SESSION['lastSupportListViewTime']) && $_SESSION['lastHelpReplyTime']>$_SESSION['lastSupportListViewTime']) || 
			    (!intval($_SESSION['lastSupportListViewTime']) && $_SESSION['lastHelpReplyTime']>UserInfo::getRemoteAuthTimestamp()) ){
				//echo 'TESTING: '.(intval($_SESSION['lastHelpReplyTime']).'-'.intval($_SESSION['lastSupportListViewTime']));
				$_SESSION['newHelpResponseWaitingTime']=time();
				return 1;
			}
		} 
		return 0;
	}
	
	
	function getDiagnosticData(){
		$brwsrData=ConcreteSupportHelper::getBrowserInfo();
		$brwsr=$brwsrData['browser'].' '.$brwsrData['fullVersion'];
		$brwsr.=' '.$brwsrData['platform'];//.' '.$brwsrData['userAgent'];    
		if($_REQUEST["pg_url"]) 
			$data['pg_url']=$_REQUEST["pg_url"]; 
		$data['browser_info']=$brwsr;
		$data['c5_version']=APP_VERSION;
		$data['php_version']=phpversion();
		$data['server_software']=$_SERVER["SERVER_SOFTWARE"]; 	
		return $data;
	}
	
	function getBrowserInfo() {
		// Note: An excellent article on browser IDs can be found at
		// http://www.zytrax.com/tech/web/browser_ids.htm
	
		$SUPERCLASS_NAMES = "gecko,mozilla,mosaic,webkit";
		$SUPERCLASS_REGX  = "(?:".str_replace(",", ")|(?:", $SUPERCLASS_NAMES).")";
	
		$SUBCLASS_NAMES   = "opera,msie,firefox,chrome,safari";
		$SUBCLASS_REGX    = "(?:".str_replace(",", ")|(?:", $SUBCLASS_NAMES).")";
	
		$browser      = "unrecognized";
		$majorVersion = "0";
		$minorVersion = "0";
		$fullVersion  = "0.0";
		$platform     = 'unrecognized';
	
		$userAgent    = strtolower($_SERVER['HTTP_USER_AGENT']);
	
		$found = preg_match("/(?P<browser>".$SUBCLASS_REGX.")(?:\D*)(?P<majorVersion>\d*)(?P<minorVersion>(?:\.\d*)*)/i",$userAgent, $matches);
		if (!$found) {
			$found = preg_match("/(?P<browser>".$SUPERCLASS_REGX.")(?:\D*)(?P<majorVersion>\d*)(?P<minorVersion>(?:\.\d*)*)/i",$userAgent, $matches);
		}
	
		if ($found) {
			$browser      = $matches["browser"];
			$majorVersion = $matches["majorVersion"];
			$minorVersion = $matches["minorVersion"];
			$fullVersion  = $matches["majorVersion"].$matches["minorVersion"];
			if ($browser == "safari") {
				if (preg_match("/version\/(?P<majorVersion>\d*)(?P<minorVersion>(?:\.\d*)*)/i",$userAgent, $matches)){
					$majorVersion = $matches["majorVersion"];
					$minorVersion = $matches["minorVersion"];
					$fullVersion  = $majorVersion.".".$minorVersion;
				}
			}
		}
	
		if (strpos($userAgent, 'linux')) {
			$platform = 'linux';
		}
		else if (strpos($userAgent, 'macintosh') || strpos($userAgent, 'mac platform x')) {
			$platform = 'mac';
		}
		else if (strpos($userAgent, 'windows') || strpos($userAgent, 'win32')) {
			$platform = 'windows';
		}
	
		return array( 
			"browser"      => $browser,
			"majorVersion" => $majorVersion,
			"minorVersion" => $minorVersion,
			"fullVersion"  => $fullVersion,
			"platform"     => $platform,
			"userAgent"    => $userAgent);
	}		
}

?>