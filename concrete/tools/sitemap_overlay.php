<?php
defined('C5_EXECUTE') or die("Access Denied.");

$sh = Loader::helper('concrete/dashboard/sitemap');
if (!$sh->canRead()) {
    die(t('Access Denied'));
}

$v = View::getInstance();
$v->requireAsset('core/sitemap');

$site = \Core::make("site")->getSite();
?>

<div class="ccm-sitemap-overlay"></div>


<script type="text/javascript">
    $(function () {
        $('.ccm-sitemap-overlay').concreteSitemap({
            siteTreeID: <?=$site->getSiteTreeID()?>,
            onClickNode: function (node) {
                ConcreteEvent.publish('SitemapSelectPage', {
                    cID: node.data.cID,
                    title: node.title,
                    instance: this
                });
            },
            displaySingleLevel: <?= $_REQUEST['display'] == 'flat' ? 'true' : 'false' ?>,
        });
    });
</script>
