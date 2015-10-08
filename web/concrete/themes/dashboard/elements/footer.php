<?
if (\Request::getInstance()->get('_ccm_dashboard_external')) {
        return;
}
?>
</div>
</div>

<?php Loader::element('footer_required', array('disableTrackingCode' => true)); ?>
<script type="text/javascript">
	ConcretePanelManager.register({'overlay': false, 'identifier': 'dashboard', 'position': 'right', url: '<?=URL::to("/ccm/system/panels/dashboard")?>'});
	ConcretePanelManager.register({'identifier': 'sitemap', 'position': 'right', url: '<?=URL::to("/ccm/system/panels/sitemap")?>'});
    <?php if (!$hideDashboardPanel) { ?>
        var panel = ConcretePanelManager.getByIdentifier('dashboard');
        panel.isOpen = true;
        panel.onPanelLoad();
    <?php } ?>

    $(function() {
        $('a[data-launch-panel=dashboard]').on('click', function() {
            setTimeout(function() {
                // needs a moment
                var panel = ConcretePanelManager.getByIdentifier('dashboard');
                if (panel.isOpen) {
                    $.cookie('dashboardPanelStatus', 'open', {path: '<?=DIR_REL?>/'});
                } else {
                    $.cookie('dashboardPanelStatus', 'closed', {path: '<?=DIR_REL?>/' });
                }
            }, 500);
        });
    });
</script>
</body>
</html>
