<?
$supportHelper=Loader::helper('concrete/support');
Loader::helper('JSON');
Loader::model('userinfo');

$responseData['loggedIn']=0; 

if( UserInfo::isRemotelyLoggedIn() ){
	$responseData['loggedIn']=1;
}

echo JsonHelper::encode($responseData);
?>