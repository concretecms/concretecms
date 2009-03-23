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

Loader::helper('JSON');

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
		
		if( strlen($response) ) return JsonHelper::decode($response);
		else return array();		
	}
	
	function usersTickets(){
		Loader::model('userinfo'); 
		$authId=UserInfo::getRemoteAuthToken();
		
		$postStr='session='.$authId;
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
		echo $response;
		
		if( strlen($response) ) $tickets = JsonHelper::decode($response);
		if(is_array($tickets)) return $tickets;
		else return array(); 
	}
}

?>