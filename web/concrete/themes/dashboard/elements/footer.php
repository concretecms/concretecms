<?
if (\Request::getInstance()->get('_ccm_dashboard_external')) {
        return;
}
?>
</div>
</div>

<? Loader::element('footer_required', array('disableTrackingCode' => true)); ?>
<?php
$upg = new \Permissions();
if (\Config::get('concrete.version') !== \Config::get('concrete.version_installed') && $upg->canUpgrade()) {
    echo \Core::make('helper/concrete/ui')->notify(
        array(
            'title'   => t('Upgrade Required!'),
            'message' => t('Your site has a required upgrade available.') .
                '<p>' . t('Click below to begin the upgrade.') . '</p>',
            'type'    => 'danger',
            'icon'    => 'exclamation',
            'buttons' => array(sprintf('<a class="btn btn-primary" href="%s">%s</a>', URL::to('/ccm/system/upgrade'), t('Upgrade')))
        ));
}
?>
<script type="text/javascript">
	ConcretePanelManager.register({'overlay': false, 'identifier': 'dashboard', 'position': 'right', url: '<?=URL::to("/ccm/system/panels/dashboard")?>'});
	ConcretePanelManager.register({'identifier': 'sitemap', 'position': 'right', url: '<?=URL::to("/ccm/system/panels/sitemap")?>'});
    <? if (!$hideDashboardPanel) { ?>
        var panel = ConcretePanelManager.getByIdentifier('dashboard');
        panel.isOpen = true;
        panel.onPanelLoad();
    <? } ?>

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
