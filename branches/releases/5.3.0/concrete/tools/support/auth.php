<?php 
$supportHelper=Loader::helper('concrete/support');
Loader::helper('json');
Loader::model('userinfo');

$responseData['loggedIn']=0; 

if($_REQUEST['logout']==1){
	UserInfo::endRemoteAuthSession();
}else{
	if( UserInfo::isRemotelyLoggedIn() ){
		$responseData['loggedIn']=1;
	}
}

echo JsonHelper::encode($responseData);
?>