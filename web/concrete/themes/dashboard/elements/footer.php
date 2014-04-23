<?
if ($_GET['_ccm_dashboard_external']) {
        return;
}
?>
</div>
</div>

<? Loader::element('footer_required', array('disableTrackingCode' => true)); ?>
<script type="text/javascript">
	ConcretePanelManager.register({'identifier': 'dashboard', 'position': 'right', url: '<?=URL::to("/ccm/system/panels/dashboard")?>'});
	ConcretePanelManager.register({'identifier': 'sitemap', 'position': 'right', url: '<?=URL::to("/ccm/system/panels/sitemap")?>'});
	var panel = ConcretePanelManager.getByIdentifier('dashboard');
	panel.isOpen = true;
	panel.onPanelLoad()
</script>
</body>
</html>
