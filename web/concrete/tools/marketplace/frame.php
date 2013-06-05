<?  defined('C5_EXECUTE') or die("Access Denied.");

$tp = new TaskPermission();
if ($tp->canInstallPackages()) { 
	Loader::library('marketplace');
	$mi = Marketplace::getInstance();
	if ($_REQUEST['complete']) { 
	
		Config::save('MARKETPLACE_SITE_TOKEN', $_POST['csToken']);
		Config::save('MARKETPLACE_SITE_URL_TOKEN', $_POST['csURLToken']);
	
		?>
		<script type="text/javascript">
			<? if ($_REQUEST['task'] == 'get') { ?>
				parent.ccm_getMarketplaceItem({mpID: '<?=$_REQUEST['mpID']?>', closeTop: true});
			<? } else if ($_REQUEST['task'] == 'open_theme_launcher') { ?>
				parent.ccm_openThemeLauncher();
			<? } else if ($_REQUEST['task'] == 'open_addon_launcher') { ?>
				parent.ccm_openAddonLauncher();
			<? } else if ($_REQUEST['task'] == 'get_item_details') { ?>
				parent.jQuery.fn.dialog.closeTop();
				parent.ccm_getMarketplaceItemDetails('<?=$_REQUEST['mpID']?>');
			<? } ?>
		</script>
	<? } else { ?>
		<script type="text/javascript" src="<?=ASSETS_URL_JAVASCRIPT?>/jquery.postmessage.js"></script>
	<?
		$completeURL = BASE_URL . REL_DIR_FILES_TOOLS_REQUIRED . '/marketplace/frame?complete=1&task=' . $_REQUEST['task'] . '&mpID=' . $_REQUEST['mpID'];
		print $mi->getMarketplaceFrame('100%', '100%', $completeURL);
	}
} else {
	print t('You do not have permission to connect to the marketplace.');
}