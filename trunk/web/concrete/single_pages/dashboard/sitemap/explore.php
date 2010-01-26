<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>

<script type="text/javascript">
$(function() {
	ccmSitemapLoad('explore');
});
</script>

<h1><span><?=t('Sitemap')?></span></h1>

<div class="ccm-dashboard-inner" >
	
	<div id="ccm-sitemap-message"></div>

	<div id="tree" class="ccm-sitemap-explore">
		<ul id="tree-root0" tree-root-node-id="0" sitemap-mode="explore" >
		<?=$listHTML?>
		</ul>
	</div>

</div>