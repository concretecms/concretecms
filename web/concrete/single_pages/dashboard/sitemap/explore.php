<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<style type="text/css">
div.ccm-sitemap-explore ul li.ccm-sitemap-explore-paging {display: none;}
</style>

<script type="text/javascript">

CCM_LAUNCHER_SITEMAP = 'explore'; // we need this for when we are moving and copying

$(function() {
	ccmSitemapLoad('<?=$instanceID?>', 'explore');
});
</script>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Sitemap'), t('Sitemap flat view lets you page through particular long lists of pages.'), 'span10 offset1', false);?>
<div class="ccm-pane-body">

<? if ($dh->canRead()) { ?>	
	<div id="ccm-sitemap-message"></div>

	<div id="tree" class="ccm-sitemap-explore">
		<ul id="tree-root0" tree-root-node-id="0" sitemap-display-mode="explore" sitemap-instance-id="<?=$instanceID?>">
		<?=$listHTML?>
		</ul>
	</div>
<? } else { ?>
	<p><?=t('You do not have access to the dashboard sitemap.')?></p>
<? } ?>

</div>	
<div class="ccm-pane-footer" id="ccm-explore-paging-footer">
	
</div>

<script type="text/javascript">
$(function() {
	$('#ccm-explore-paging-footer').html($('li.ccm-sitemap-explore-paging').html());
});
</script>
<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);