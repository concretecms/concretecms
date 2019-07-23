<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div class="ccm-sitemap-overlay-<?= $overlayID; ?>"></div>

<script type="text/javascript">
    $(function () {
        $('.ccm-sitemap-overlay-<?= $overlayID; ?>').concreteSitemap({
            onClickNode: function (node) {
                ConcreteEvent.publish('SitemapSelectPage', {
                    cID: node.data.cID,
                    title: node.title,
                    instance: this
                });
            },
            cParentID: <?= $cParentID; ?>,
            displayNodePagination: <?= (isset($display) && $display === 'flat') ? 'true' : 'false'; ?>,
            displaySingleLevel: <?= (isset($display) && $display === 'flat') ? 'true' : 'false'; ?>,
            isSitemapOverlay: true,
        });
    });
</script>
