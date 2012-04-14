<?php
defined('C5_EXECUTE') or die("Access Denied.");

Loader::model('collection_types');
Loader::library('marketplace');
$mi = Marketplace::getInstance();
$tp = new TaskPermission();
if ($mi->isConnected() && $tp->canInstallPackages()) { 
	
	$previewCID=intval($_REQUEST['previewCID']);
	$themeCID=intval($_REQUEST['themeCID']);
	$themeHandle=$_REQUEST['themeHandle'];

	$postStr= '&themeHandle='.$themeHandle.'&ctID='.$ctID.'&ctHandle='.$ctHandle;
	
	if (!function_exists('curl_init')) { ?>
		<div><?=t('curl must be enabled to preview external themes.')?></div>
	<? }else{
		$curl_handle = curl_init();
		curl_setopt($curl_handle, CURLOPT_URL, MARKETPLACE_THEME_PREVIEW_URL);
		curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $postStr);
		//curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
		$contents = curl_exec($curl_handle);
		curl_close($curl_handle);
		echo $contents;
	} 
}