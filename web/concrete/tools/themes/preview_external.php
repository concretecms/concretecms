<?php
defined('C5_EXECUTE') or die("Access Denied.");


$mi = Marketplace::getInstance();
$tp = new TaskPermission();
if ($mi->isConnected() && $tp->canInstallPackages()) {

	$previewCID=intval($_REQUEST['previewCID']);
	$themeCID=intval($_REQUEST['themeCID']);
	$themeHandle=$_REQUEST['themeHandle'];

	$postStr= '&themeHandle='.$themeHandle.'&ptID='.$ptID.'&ctHandle='.$ctHandle;

	if (!function_exists('curl_init')) { ?>
		<div><?=t('curl must be enabled to preview external themes.')?></div>
	<? }else{
		$curl_handle = curl_init();

		// Check to see if there are proxy settings
		if (Config::get('concrete.proxy.host') != null) {
			@curl_setopt($curl_handle, CURLOPT_PROXY, Config::get('concrete.proxy.host'));
			@curl_setopt($curl_handle, CURLOPT_PROXYPORT, Config::get('concrete.proxy.port'));

			// Check if there is a username/password to access the proxy
			if (Config::get('concrete.proxy.user') != null) {
				@curl_setopt($curl_handle, CURLOPT_PROXYUSERPWD, Config::get('concrete.proxy.user') . ':' . Config::get('concrete.proxy.password'));
			}
		}

        $url = Config::get('concrete.urls.concrete5') . Config::get('concrete.urls.paths.theme_preview');
		curl_setopt($curl_handle, CURLOPT_URL, $url);
		curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $postStr);
		//curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
		$contents = curl_exec($curl_handle);
		curl_close($curl_handle);
		echo $contents;
	}
}
