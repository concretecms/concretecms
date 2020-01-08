<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div class="ccm-ui" id="ccm-sitemap-search-selector">

    <?php
        echo $concrete_ui->tabs([
            ['sitemap-sitemap', t('Full Sitemap')],
            ['sitemap-explore', t('Flat View')],
            ['sitemap-search', t('Search')],
        ]);
    ?>
    
    <div class="tab-content">
        <div id="sitemap-sitemap" class="tab-pane active"></div>

        <div id="sitemap-explore" class="tab-pane"></div>

        <div id="sitemap-search" class="tab-pane"></div>
    </div>


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

        if ($('#sitemap-' + type).html() == '') {
            jQuery.fn.dialog.showLoader();
            $('#sitemap-' + type).load(url, function() {
                jQuery.fn.dialog.hideLoader();
            });
        }
    }

    <?php if ($selectMode == 'move_copy_delete') {
       ?>
    ConcreteEvent.unsubscribe('SitemapSelectPage.search');

    var subscription = function (e, data) {
        Concrete.event.unsubscribe(e);
        url = CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/page/drag_request?dragMode=none&origCID=<?=$cID?>&destCID=' + data.cID;
        $.fn.dialog.open({
            width: 520,
            height: 'auto',
            href: url,
            title: ccmi18n_sitemap.moveCopyPage,
            onDirectClose: function() {
                ConcreteEvent.subscribe('SitemapSelectPage.search', subscription);
            }
        });
    };
    ConcreteEvent.subscribe('SitemapSelectPage.search', subscription);
    <?php

    }?>


    $(function() {
        var cParentID = 0;
        var sst = jQuery.cookie('ccm-sitemap-selector-tab');
        if (sst !== 'explore' && sst !== 'search') {
            sst = 'sitemap';
        }
        $("a[href='#sitemap-" + sst + "']").parent().addClass('active');
        ccm_sitemapSearchSelectorHideBottom();
        $("a[href='#sitemap-sitemap']").click(function() {
            loadSitemapOverlay('sitemap', CCM_DISPATCHER_FILENAME + '/ccm/system/page/sitemap_overlay');
        });
        $("a[href='#sitemap-explore']").click(function() {
            loadSitemapOverlay('explore', CCM_DISPATCHER_FILENAME + '/ccm/system/page/sitemap_overlay?display=flat&cParentID=' + cParentID);
        });
        $("a[href='#sitemap-search']").click(function() {
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
