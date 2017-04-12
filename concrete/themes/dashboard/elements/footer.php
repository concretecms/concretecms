<?php
if (Request::getInstance()->get('_ccm_dashboard_external')) {
    return;
}
?>
</div>
</div>

<?php View::element('footer_required', ['disableTrackingCode' => true]); ?>
<script type="text/javascript">
(function() {
    ConcretePanelManager.register({'overlay': false, 'identifier': 'dashboard', 'position': 'right', url: '<?=URL::to('/ccm/system/panels/dashboard')?>'});
    ConcretePanelManager.register({'identifier': 'sitemap', 'position': 'right', url: '<?=URL::to('/ccm/system/panels/sitemap')?>'});
    var panel = ConcretePanelManager.getByIdentifier('dashboard');
    <?php
    if (!(isset($hideDashboardPanel) && $hideDashboardPanel)) {
        ?>
        panel.isOpen = true;
        panel.onPanelLoad();
        <?php 
    }
    ?>
    var savePanelStatus = false;
    ConcreteEvent.subscribe('PanelOpen', function(e, data) {
        if (savePanelStatus && data.panel === panel) {
            $.cookie('dashboardPanelStatus', null, {path: '<?=DIR_REL?>/'});
            savePanelStatus = false;
        }
    });
    ConcreteEvent.subscribe('PanelClose', function(e, data) {
        if (savePanelStatus && data.panel === panel) {
            $.cookie('dashboardPanelStatus', 'closed', {path: '<?=DIR_REL?>/'});
            savePanelStatus = false;
        }
    });
    $(document).ready(function() {
        $('a[data-launch-panel=dashboard]').on('click', function() {
            savePanelStatus = true;
        });
    });
})();
</script>
</body>
</html>
