<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>

<script type="text/javascript">
	var CCM_SITEMAP_MODE = 'explore';
	var CCM_SITEMAP_EXPLORE_NODE = '<?=$nodeID?>';
</script>

<h1><span><?=t('Sitemap')?></span></h1>

<div class="ccm-dashboard-inner" >
	
	<div id="ccm-sitemap-message"></div>

	<div id="tree" style="margin-left: 0px !important">
		<ul id="tree-root0">
		<?=$listHTML?>
		</ul>
	</div>

</div>