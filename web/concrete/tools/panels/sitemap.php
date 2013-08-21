<?
defined('C5_EXECUTE') or die("Access Denied.");
$r = Request::get();
$r->requireAsset('core/sitemap');
?>


<div id="ccm-sitemap-panel-sitemap"></div>


<script type="text/javascript">
$(function() {
	$('#ccm-sitemap-panel-sitemap').ccmsitemap();
});
</script>


