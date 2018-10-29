<?php
defined('C5_EXECUTE') or die("Access Denied.");

$app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();

$sh = $app->make('helper/concrete/dashboard/sitemap');
if (!$sh->canRead()) {
    die(t('Access Denied'));
}

$v = View::getInstance();
$v->requireAsset('core/sitemap');

$overlayID = uniqid();

?>

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
            displaySingleLevel: <?= (isset($_REQUEST['display']) && $_REQUEST['display'] === 'flat') ? 'true' : 'false'; ?>,
        });
    });
</script>
