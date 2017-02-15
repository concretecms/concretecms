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
    ConcreteEvent.subscribe('PanelOpen', function(e, data) {
        if (data.panel === panel) {
            $.cookie('dashboardPanelStatus', null, {path: '<?=DIR_REL?>/'});
        }
    });
    ConcreteEvent.subscribe('PanelClose', function(e, data) {
        if (data.panel === panel) {
            $.cookie('dashboardPanelStatus', 'closed', {path: '<?=DIR_REL?>/'});
        }
    });
})();
</script>
</body>
</html>
