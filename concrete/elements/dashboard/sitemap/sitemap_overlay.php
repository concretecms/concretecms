<?php
defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var string $overlayID
 * @var int $cParentID
 * @var string $display
 * @var bool $includeSystemPages
 */
?>

<div class="ccm-sitemap-overlay-<?= $overlayID; ?>"></div>

<script>
$(function () {
    $('.ccm-sitemap-overlay-<?= $overlayID ?>').concreteSitemap({ 
        onClickNode: function (node) {
            ConcreteEvent.publish('SitemapSelectPage', {
                cID: node.data.cID,
                title: node.title,
                instance: this
            });
        },
        cParentID: <?= $cParentID; ?>,
        displayNodePagination: <?= $display === 'flat' ? 'true' : 'false' ?>,
        displaySingleLevel: <?= $display === 'flat' ? 'true' : 'false' ?>,
        includeSystemPages: <?= $includeSystemPages ? 'true' : 'false' ?>,
        isSitemapOverlay: true,
    });
});
</script>
