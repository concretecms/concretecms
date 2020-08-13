<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div class="ccm-ui" id="ccm-sitemap-search-selector">
    <div class="container-fluid h-100">
        <div class="row h-100">
            <div class="col-3 border-right">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a data-toggle="tab" id="sitemap-sitemap-tab" href="#sitemap-sitemap" class="nav-link active"><?=t('Full Sitemap')?></a>
                    </li>
                    <li class="nav-item">
                        <a data-toggle="tab" id="sitemap-explore-tab" href="#sitemap-explore" class="nav-link"><?=t('Flat Sitemap')?></a>
                    </li>
                    <li class="nav-item">
                        <a data-toggle="tab" id="sitemap-search-tab" href="#sitemap-search" class="nav-link"><?=t('Search')?></a>
                    </li>
                </ul>
            </div>
            <div class="col-9">
                <div class="tab-content">
                    <div id="sitemap-sitemap" class="tab-pane active"></div>
                    <div id="sitemap-explore" class="tab-pane"></div>
                    <div id="sitemap-search" class="tab-pane">
                        <?php $uniqid = uniqid() ?>
                        <div data-concrete-page-chooser-search="<?= $uniqid ?>">
                            <concrete-page-chooser-search/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script type="text/javascript">

    loadSitemapOverlay = function(type, url) {
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
        $("a[href='#sitemap-sitemap']").click(function() {
            loadSitemapOverlay('sitemap', CCM_DISPATCHER_FILENAME + '/ccm/system/page/sitemap_overlay');
        });
        $("a[href='#sitemap-explore']").click(function() {
            loadSitemapOverlay('explore', CCM_DISPATCHER_FILENAME + '/ccm/system/page/sitemap_overlay?display=flat&cParentID=' + cParentID);
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

        Concrete.Vue.activateContext('cms', function (Vue, config) {
            new Vue({
                el: 'div[data-concrete-page-chooser-search="<?= $uniqid ?>"]',
                components: config.components
            });
        });
    });
</script>
