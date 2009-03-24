<?
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
		
		$searchURL=KNOWLEDGE_BASE_URL.'?keywords='.urlencode($question).'&format=JSON'; 
		
		if (function_exists('curl_init')) {
			$curl_handle = curl_init();
			curl_setopt($curl_handle, CURLOPT_URL, $searchURL);
			curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
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
		
		//$postStr='question='.urlencode($data['question']).'&notes='.urlencode($data['notes']).'&session='.$authId;
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
			if( $responseObj->lastReplyTime ) 
				$_SESSION['lastHelpReplyTime']=$responseObj->lastReplyTime;
			return $responseObj;
		}else return new Object(); 
	}
	
	function hasNewHelpResponse(){
		//user hasn't checked their tickets since it was noticed they have a new reply
		if($_SESSION['newHelpResponseWaiting']==1) return true;
		
		$authData = UserInfo::getAuthData();
		if( $authData['auth_token'] ){
		
			//only check every 5 minutes
			if( intval($_SESSION['lastHelpWaitingCheckTime'])<(time()-300) ){
				if(UserInfo::isRemotelyLoggedIn()){
					$response=ConcreteSupportHelper::usersTickets();				
					$_SESSION['lastHelpWaitingCheckTime']=time(); 
				}
			}
			
			//since last time the user viewed the tickets list, or since last login
			if( (intval($_SESSION['lastSupportListViewTime']) && $_SESSION['lastHelpReplyTime']>$_SESSION['lastSupportListViewTime']) || 
			    (!intval($_SESSION['lastSupportListViewTime']) && $_SESSION['lastHelpReplyTime']>UserInfo::getRemoteAuthTimestamp()) ){
				
				//echo 'NEW RESPONSE WAITING!!!'.(intval($_SESSION['lastHelpReplyTime'])-intval($_COOKIE['lastSupportListViewTime']));
				$_SESSION['newHelpResponseWaiting']=1;
				return true;
			}
		}
		return false;
	}
}

?>