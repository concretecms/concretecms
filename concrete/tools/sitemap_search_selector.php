<?php
defined('C5_EXECUTE') or die('Access Denied.');

$app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();

$sh = $app->make('helper/concrete/dashboard/sitemap');
if (!$sh->canRead()) {
    die(t('Access Denied.') . ' ' . t('You do not have access to the sitemap.'));
}

?>

<div class="ccm-ui" id="ccm-sitemap-search-selector">

    <?php
        echo $app->make('helper/concrete/ui')->tabs([
            ['sitemap', t('Full Sitemap')],
            ['explore', t('Flat View')],
            ['search', t('Search')],
        ]);
    ?>

    <div id="ccm-tab-content-sitemap" class="ccm-tab-content"></div>

    <div id="ccm-tab-content-explore" class="ccm-tab-content"></div>

    <div id="ccm-tab-content-search" class="ccm-tab-content"></div>

</div>

<script type="text/javascript">
    ccm_sitemapSearchSelectorHideBottom = function() {
        $('#ccm-sitemap-search-selector').parent().parent().find('.ui-dialog-buttonpane').hide();
    }

    ccm_sitemapSearchSelectorShowBottom = function() {
        $('#ccm-sitemap-search-selector').parent().parent().find('.ui-dialog-buttonpane').show();
    }

    loadSitemapOverlay = function(type, url) {
        jQuery.cookie('ccm-sitemap-selector-tab', type, { path: '<?=DIR_REL; ?>/' });

        switch (type) {
            case 'search':
                ccm_sitemapSearchSelectorShowBottom();
                break;
            default:
                ccm_sitemapSearchSelectorHideBottom();
                break;
        }

        if ($('#ccm-tab-content-' + type).html() == '') {
            jQuery.fn.dialog.showLoader();
            $('#ccm-tab-content-' + type).load(url, function() {
                jQuery.fn.dialog.hideLoader();
            });
        }
    }

    $(function() {
        var cParentID = 0;
        var sst = jQuery.cookie('ccm-sitemap-selector-tab');
        if (sst !== 'explore' && sst !== 'search') {
            sst = 'sitemap';
        }
        $('a[data-tab=' + sst + ']').parent().addClass('active');
        ccm_sitemapSearchSelectorHideBottom();
        $('a[data-tab=sitemap]').click(function() {
            loadSitemapOverlay('sitemap', CCM_DISPATCHER_FILENAME + '/ccm/system/page/sitemap_overlay');
        });
        $('a[data-tab=explore]').click(function() {
            loadSitemapOverlay('explore', CCM_DISPATCHER_FILENAME + '/ccm/system/page/sitemap_overlay?display=flat&cParentID=' + cParentID);
        });
        $('a[data-tab=search]').click(function() {
            loadSitemapOverlay('search', '<?= URL::to('/ccm/system/dialogs/page/search'); ?>');
        });

        $('#ccm-sitemap-search-selector ul li.active a').click();

        $('#ccm-tab-content-sitemap').on('click', '.ccm-sitemap-open-flat-view', function(event) {
            var node = $.ui.fancytree.getNode(event);
            if (node && node.data && node.data.cParentID) {
                cParentID = node.data.cParentID;
            }
            $('#ccm-tab-content-explore').html('');
            $('a[data-tab=explore]').trigger('click');
        });
    });
</script>
