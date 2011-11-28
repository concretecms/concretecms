<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<style type="text/css">
div.ccm-pane-body {padding-top: 0px; padding-right: 0px; padding-left: 0px}
</style>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Connect to Community'), false, 'span16')?>
<? 
	$mi = Marketplace::getInstance();
	print $mi->getMarketplaceFrame('100%', '300', false, $startStep);
?>
<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper();?>
