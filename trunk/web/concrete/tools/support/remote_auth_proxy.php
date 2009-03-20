<?
//the remote login stuff uses ajax, so there's the cross domain issue
//this file converts a local ajax call into the remote cUrl call to get around that issue
//yay.

Loader::helper('JSON');
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
$remoteLoginURL = CONCRETE5_ORG_URL.'/login/-/'.$action.'/';
$postStr = http_build_query($_POST, '', '&');
$curl_handle = curl_init();
curl_setopt($curl_handle, CURLOPT_URL, $remoteLoginURL);
curl_setopt($curl_handle, CURLOPT_POST, true);
curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $postStr);
curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 30);
curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec($curl_handle); 

//save the token if it was a successful login
$responseData = JsonHelper::decode($response);
if( $responseData->success && $responseData->auth_token )
	UserInfo::setRemoteAuthToken( $responseData->auth_token );

//return the json response to the ajax script, just as if it were a standard login
echo $response;

?>