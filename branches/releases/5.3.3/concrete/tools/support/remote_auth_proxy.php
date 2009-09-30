<?php 
//the remote login stuff uses ajax, so there's the cross domain issue
//this file converts a local ajax call into the remote cUrl call to get around that issue
//yay.

Loader::helper('json');
Loader::model('userinfo');

//does this request have one of the allowed actions?
$action=$_REQUEST['action'];
if(!in_array($action,array('do_login','forgot_password','do_register'))){
	echo t('Remote Authentication - Invalid action');
	die;
}

//needed vars for a remote login
$_POST['format']='JSON';
$_POST['remote']=1;
$_POST['timestamp']=time();

//save the info for generating the token
UserInfo::setRemoteAuthTimestamp( $_POST['timestamp'] );
UserInfo::setRemoteAuthUserName( $_POST['uName'] );

//send the data to the remote login page
if($action=='do_register') 
	 $remoteLoginURL = CONCRETE5_ORG_URL.'/register/-/'.$action.'/';
else $remoteLoginURL = CONCRETE5_ORG_URL.'/login/-/'.$action.'/';

$postStr = http_build_query($_POST, '', '&');
if (function_exists('curl_init')) { 
	$curl_handle = curl_init();
	curl_setopt($curl_handle, CURLOPT_URL, $remoteLoginURL);
	curl_setopt($curl_handle, CURLOPT_POST, true);
	curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $postStr);
	curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 30);
	curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
	$response = curl_exec($curl_handle); 
	$responseData = JsonHelper::decode($response);
	$responseData->uName = $_POST['uName'];
} else {
	$responseData = new stdClass;
	$responseData->error = t('Error: curl must be enabled to proceed.');
}

//save the authentication token and uID if it was a successful login
$responseExtra = JsonHelper::encode($responseData);
if( $responseData->success && $responseData->auth_token ){
	UserInfo::setRemoteAuthToken( $responseData->auth_token );
	UserInfo::setRemoteAuthUserId( $responseData->uID );
	UserInfo::setRemoteAuthInSupportGroup( $responseData->in_support_group );
}

//return the json response to the ajax script, just as if it were a standard login
echo $responseExtra;

?>
